/*
 * ACOJ Judge Client
 * ./io_mysql.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
#include<mysql.h>
#include<cstring>
#include<set>
using namespace std;
class database_s{
	MYSQL*mysql;
	public:
	void init(){
		mysql=mysql_init(0);
	}
	bool open(){
		MYSQL*r=mysql_real_connect(mysql,
				config.mysql_host.c_str(),
				config.mysql_username.c_str(),
				config.mysql_password.c_str(),
				config.mysql_database.c_str(),
				0,0,0);
		if(!r)
			fprintf(stderr,"Failed to connect to database server: Error:\n%s\n",mysql_error(mysql));
		return(bool)r;
	}
	void close(){
		mysql_close(mysql);
	}
	void assume_testdata_exists(int id);
	bool select_submission(submission_s&submission);
	problem_s select_problem(submission_s&submission);
	vector<testdata_s>select_testdata(problem_s&problem);
	rater_s select_rater(problem_s&problem);
	void update_feedback(
			submission_s&submission,
			int id_testdata,
			feedback_s&feedback);
	void update_verdict(submission_s&submission,verdict_s&verdict);
}database;
void database_s::assume_testdata_exists(int id){
	static set<int>memory_existence_testdata;
	set<int>&mem=memory_existence_testdata;
	if(mem.find(id)!=mem.end())
		return;
	mem.insert(id);
	char path_testdata_input[1024];
	char path_testdata_output[1024];
	sprintf(path_testdata_input,"%s%i.in",
			config.path_testdata.c_str(),id);
	sprintf(path_testdata_output,"%s%i.out",
			config.path_testdata.c_str(),id);
	if(access(path_testdata_input,F_OK)==-1){
		/*
		   This codeblock did cause segmentation fault.
		   Alt 2013-11-13.
		 */
		char query[1024];
		sprintf(query,"\
				SELECT\
				`input`,\
				`output`\
				FROM `testdata`\
				WHERE `id`='%i';",id);

		mysql_query(mysql,query);
		MYSQL_RES *res=mysql_use_result(mysql);
		MYSQL_ROW row=mysql_fetch_row(res);

		const int buffer_size=1<<28;
		static char buffer[buffer_size];

		strcpy(buffer,row[0]);
		cvtnlunix(buffer);
		FILE*file_testdata_input=fopen(path_testdata_input,"w");
		fputs(buffer,file_testdata_input);
		fclose(file_testdata_input);

		strcpy(buffer,row[1]);
		cvtnlunix(buffer);
		FILE*file_testdata_output=fopen(path_testdata_output,"w");
		fputs(buffer,file_testdata_output);
		fclose(file_testdata_output);

		mysql_free_result(res);
	}
}
bool database_s::select_submission(submission_s&submission){
	char command[1024]="\
			    SELECT\
			    `id`,\
			    `id_problem`,\
			    `language`,\
			    `sourcecode`\
			    FROM `submissions`\
			    WHERE `status`='0'\
			    ORDER BY `id` DESC\
			    LIMIT 1;";
	mysql_query(mysql,command);
	MYSQL_RES*res=mysql_use_result(mysql);
	MYSQL_ROW row=mysql_fetch_row(res);
	if(row){
		submission.id=atoi(row[0]);
		submission.id_problem=atoi(row[1]);
		submission.language=atoi(row[2]);
		strcpy(submission.sourcecode,row[3]);
	}
	mysql_free_result(res);
	return(bool)row;
}
problem_s database_s::select_problem(submission_s&submission){
	problem_s problem;
	char query[1024];
	sprintf(query,"SELECT\
			`id`,\
			`id_rater`\
			FROM `problems`\
			WHERE `id`='%i';"
			,submission.id_problem);
	mysql_query(mysql,query);
	MYSQL_RES*res=mysql_use_result(mysql);
	MYSQL_ROW row=mysql_fetch_row(res);
	problem.id=atoi(row[0]);
	problem.id_judge=atoi(row[1]);
	mysql_free_result(res);
	return problem;
}
vector<testdata_s>database_s::select_testdata(problem_s&problem){
	vector<testdata_s>testdatas;
	char query[1024];
	sprintf(query,"SELECT\
			`id`,\
			`limit_time_ms`,\
			`limit_memory_byte`,\
			`limit_stack_byte`\
			FROM `testdata`\
			WHERE `problem`='%i'\
			ORDER BY `id_group`,`id`;"
			,problem.id);
	mysql_query(mysql,query);
	MYSQL_RES*res=mysql_use_result(mysql);
	MYSQL_ROW row;
	while(row=mysql_fetch_row(res)){
		testdata_s testdata;
		testdata.id=atoi(row[0]);
		testdata.limit_time=atoi(row[1]);
		testdata.limit_memory=atoi(row[2])/1024;
		testdata.limit_stack=atoi(row[3]);
		testdatas.push_back(testdata);
	}
	mysql_free_result(res);
	for(auto&x:testdatas)
		database.assume_testdata_exists(x.id);
	return testdatas;
}
rater_s database_s::select_rater(problem_s&problem){
	rater_s rater;
	char query[1024];
	sprintf(query,"SELECT\
			`id`,\
			`interactive`,\
			`source`\
			FROM `raters`\
			WHERE `id`='%i';"
			,problem.id_judge);
	mysql_query(mysql,query);
	MYSQL_RES*res=mysql_use_result(mysql);
	MYSQL_ROW row=mysql_fetch_row(res);
	rater.id=atoi(row[0]);
	rater.interactive=atoi(row[1]);
	strcpy(rater.sourcecode,row[2]);
	mysql_free_result(res);
	return rater;
}
void database_s::update_feedback(
		submission_s&submission,
		int id_testdata,
		feedback_s&feedback
		){
	char query[65536];
	sprintf(query,"INSERT INTO `tests`\
			(`id_submission`,\
			 `id_testdata`,\
			 `code_runtime_error`,\
			 `code_invalid_systemcall`,\
			 `usage_time`,\
			 `usage_memory`,\
			 `status`,\
			 `rating`\
			) VALUE (\
				'%i',\
				'%i',\
				'%i',\
				'%i',\
				'%i',\
				'%i',\
				'%i',\
				'%f'\
				);",
			submission.id,
			id_testdata,
			feedback.execution.error,
			feedback.execution.invalid_systemcall,
			feedback.execution.usage_time_ms,
			feedback.execution.usage_memory_kib,
			result_to_status[feedback.execution.result],
			feedback.rating
				);
	mysql_query(mysql,query);
}
void database_s::update_verdict(
		submission_s&submission,
		verdict_s&verdict
		){
	char query[65536];
	char message_escaped[2*65536];
	// status and compilation messages
	status_e status;
	if(verdict.compilation.error)
		status=CE;
	else{
		result_e min_result=accepted_;
		for(auto&x:verdict.feedbacks)
			min_result=min(min_result,
					x.second.execution.result);
		status=result_to_status[min_result];
	}
	float sum_rating=0;
	for(auto&x:verdict.feedbacks)
		sum_rating+=x.second.rating;
	mysql_real_escape_string(
			mysql,
			message_escaped,
			verdict.compilation.message,
			strlen(verdict.compilation.message)
			);
	sprintf(query,"UPDATE `submissions`\
			SET\
			`status`='%i',\
			`rating`='%f',\
			`compilation_messages`='%s'\
			WHERE `id`='%i';",
			status,
			sum_rating,
			message_escaped,
			submission.id);
	mysql_query(mysql,query);
	// break if compilation error
	if(verdict.compilation.error)
		return;
	// feedbacks
	for(auto&x:verdict.feedbacks){
		int id_testdata=x.first;
		feedback_s feedback=x.second;
		update_feedback(submission,id_testdata,feedback);
	}
	// total time and memory usage.
	int sum_usage_time_ms=0;
	int sum_usage_memory_kib=0;
	for(auto&x:verdict.feedbacks){
		sum_usage_time_ms+=x.second.execution.usage_time_ms;
		sum_usage_memory_kib+=x.second.execution.usage_memory_kib;
	}
	sprintf(query,"UPDATE `submissions`\
			SET\
			`usage_time_ms`='%i',\
			`usage_memory_kib`='%i'\
			WHERE `id`='%i';",
			sum_usage_time_ms,
			sum_usage_memory_kib,
			submission.id);
	mysql_query(mysql,query);
}
