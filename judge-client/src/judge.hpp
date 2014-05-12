/*
   acoj kernel
   ./judge.hpp
 */
#include<cstring>
#include"compiler.hpp"
#include"executer.hpp"
#include"rater.hpp"
class feedback_s{
	public:
		execution_result_s execution;
		float rating;
		feedback_s(){}
		feedback_s(submission_s&submission,testdata_s&testdata,rater_s&rater);
};
feedback_s::feedback_s(
		submission_s&submission,
		testdata_s&testdata,
		rater_s&rater){
	rating=0;
	// execution, break if runtime error
	execution=execution_result_s(submission,testdata,rater);
	// return if execution is not accepted
	if(execution.result!=accepted_)
		return;
	rating=rate(testdata,rater);
	execution.result=
		rating>=0.5
		?accepted_
		:wrong_answer_;
}
#include<map>
class verdict_s{
	public:
		compilation_result_s compilation;
		map<int,feedback_s>feedbacks;
		verdict_s(
				submission_s&submission,
				vector<testdata_s>testdatas,
				rater_s&rater
			 );
};
verdict_s::verdict_s(
		submission_s&submission,
		vector<testdata_s>testdatas,
		rater_s&rater
		){
	const int max_path=1<<10;
	const int max_command=1<<10;
	// prepare the rater
	{
		char path_rater_source[max_path];
		sprintf(path_rater_source,"%srater.cpp",config.path_testarea.c_str());
		// save the source code of rater into file
		FILE*file_rater_source=fopen(path_rater_source,"w");
		fputs(rater.sourcecode,file_rater_source);
		fclose(file_rater_source);
		// compile the rater
		int pid_child=fork();
		if(!pid_child){
			system("g++ -o ./testarea/rater.out ./testarea/rater.cpp");
			exit(EXIT_SUCCESS);
		}
		waitpid(pid_child,0,0);
		remove(path_rater_source);
	}
	// compile, break if error
	compilation=compilation_result_s(submission);
	if(compilation.error)
		return;
	// for each testdata, do test
	printf("ID(SUB)\tID(TD)\tTIM(ms)\tMEM(KiB)\tRESULT\tRATING\n");
	for(auto&testdata:testdatas){
		if(submission.language==6){
			testdata.limit_memory+=1048576;
			testdata.limit_stack+=1048576*1024;
		}
		feedbacks[testdata.id]=feedback_s(submission,testdata,rater);
		printf("%7i\t%7i\t%7i\t%7i\t\t%s\t%f\n",
				submission.id,
				testdata.id,
				feedbacks[testdata.id].execution.usage_time_ms,
				feedbacks[testdata.id].execution.usage_memory_kib,
				name_status_short[result_to_status[
				feedbacks[testdata.id].execution.result]],
				feedbacks[testdata.id].rating
		      );
	}
	// remove the binary file.
	char path_execute[max_path];
	char path_rater[max_path];
	sprintf(path_execute,"%smain.out",config.path_testarea.c_str());
	remove(path_execute);
	sprintf(path_rater,"%srater.out",config.path_testarea.c_str());
	remove(path_rater);
}
