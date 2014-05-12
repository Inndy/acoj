/*
 * ACOJ Judge Client
 * ./main.cpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
/*
   main{
   	database{
	}
   	judge{
		compiler;
		executer{
			child;
		}
		rater;
	}
   }
 */
/*
   有關於命名規則：
   如果一個變數是有單位的，但其單位並非 ms 或 KiB ，則需要後綴註明。
 */
#include<unistd.h>
#include<cstdio>
using namespace std;
#include"debug.hpp"
#include"function.hpp"
#include"const.hpp"
#include"struct.hpp"
#include"config.hpp"
#include"judge.hpp"
#include"io_mysql.hpp"
void prepare_and_judge(submission_s&submission){
#ifdef ACOJ_DEBUG
	fprintf(stderr,"submission %i:\n",submission.id);
#endif
	// prepare problem
	problem_s problem=database.select_problem(submission);
	// prepare testdatas
	vector<testdata_s>testdatas=database.select_testdata(problem);
	// prepare rater
	rater_s rater=database.select_rater(problem);
	// judge
	verdict_s verdict(submission,testdatas,rater);
	// update
	database.update_verdict(submission,verdict);
}
void wait_submissions(int&previous_state,int&state){
	int t=(int)time(0),seconds=t%60,minutes=(t/=60)%60,hours=(t/=60)%24;
	submission_s submission;
	if(database.select_submission(submission)){
		state=1;
		if(previous_state!=state)
			printf("\n\n");
		printf("%02i:%02i:%02i submission got.\n"
				,(hours+8)%24,minutes,seconds);
		fflush(stdout);
		prepare_and_judge(submission);
		printf("\n");
	}else{
		state=2;
		if(previous_state==state)
			printf("\r");
		printf("%02i:%02i:%02i waiting for submissions ..."
				,(hours+8)%24,minutes,seconds);
		fflush(stdout);
	}
}
int main(int argc,char*argv[]){
#ifdef ACOJ_DEBUG
	freopen("./error.log","a",stderr);
#endif
	static const int waiting_time=(int)1e5;
	nice(-15);	// adjustment: -15
	config.load();
	config.ensure_paths_exist();
	int previous_state=2;
	while(1){
		int state=0;
		// selecting
		database.init();
		if(database.open()){
			wait_submissions(previous_state,state);
		}
		database.close();
		// selecting done
		// sleep if waiting
		if(state==2)
			usleep(waiting_time);
		previous_state=state;
	}
	return EXIT_SUCCESS;
}
