/*
 * ACOJ Judge Client
 * ./executer_child.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
#include<sched.h>
void setaffinity(int id_core){
	cpu_set_t set;
	CPU_ZERO(&set);
	CPU_SET(id_core,&set);
	sched_setaffinity(0,config.count_cores,&set);
}
#include<sys/resource.h>
void limit_sample_resource(submission_s&submission,testdata_s&testdata){
	rlimit r;
	// the stack limit
	r.rlim_cur=r.rlim_max=testdata.limit_stack;
	setrlimit(RLIMIT_STACK,&r);
	// the process limit (for the fork bomb)
	r.rlim_cur=r.rlim_max=1;
	setrlimit(RLIMIT_NPROC,&r);
}
int execute_child_process(
		submission_s&submission,
		testdata_s&testdata
		){
	int pid=fork();
	if(pid)
		return pid;
	ptrace(PTRACE_TRACEME,pid,NULL,NULL);
	nice(30);	// adjustment=15
	setaffinity(1%config.count_cores);
	char path_input[256],
	     path_newroot[256],
	     path_output[256],
	     path_stderr[256],
	     path_execute[256];
	sprintf(path_input,"%s%i.in",
			config.path_testdata.c_str(),
			testdata.id);
	sprintf(path_newroot,"%s",
			config.path_testarea.c_str()
	       );
	sprintf(path_output,"./output.txt");
	sprintf(path_stderr,"./stderr.txt");
	sprintf(path_execute,"./main.out");
	freopen(path_input,"r",stdin);
	chdir(path_newroot);
	/*
	   Temporarily deprecated on 2013-11-21.
	   Change root can be a hard work.
	   chroot(path_newroot);
	 */
	freopen(path_output,"w",stdout);
	freopen(path_stderr,"w",stderr);
	limit_sample_resource(submission,testdata);
	setuid(config.uid_acoj_executer);
	if(0<=submission.language&&submission.language<6)
		execl(path_execute,path_execute,NULL);
	else if(6<=submission.language&&submission.language<7)
		execl("/usr/lib/jvm/java-1.7.0-openjdk-i386/bin/java",
				"java",
				"main",
				NULL);
}
#include<utility>
pair<int,int>execute_child_process_interactive(
		submission_s&submission,
		testdata_s&testdata
		){
	const int max_path=1<<10;
	char path_testdata_input[max_path];
	char path_testdata_output[max_path];
	char path_testarea[max_path];
	char path_execute[max_path];
	int fd_rater_to_sample[2];
	int fd_sample_to_rater[2];
	sprintf(path_testdata_input,
			"../%s%i.in",
			config.path_testdata.c_str(),
			testdata.id
	       );
	sprintf(path_testdata_output,
			"../%s%i.out",
			config.path_testdata.c_str(),
			testdata.id
	       );
	sprintf(path_testarea,"%s",config.path_testarea.c_str());
	pipe(fd_rater_to_sample);
	pipe(fd_sample_to_rater);
	int pid_rater=fork();
	if(!pid_rater){
		char path_rating[max_path];
		sprintf(path_execute,"./rater.out");
		sprintf(path_rating,"./rating.txt");
		chdir(path_testarea);
		close(fd_rater_to_sample[0]);
		close(fd_sample_to_rater[1]);
		close(0);
		dup(fd_sample_to_rater[0]);
		close(1);
		dup(fd_rater_to_sample[1]);
		//setuid(config.uid_acoj_rater);
		int r;
		r=execl(
				path_execute,
				path_execute,
				path_testdata_input,
				path_testdata_output,
				"",
				path_rating,
				NULL);
		if(r){
			freopen("./error.executer.rater.txt","w",stdout);
			printf("%i: %s\nexecuting rater (executer) error\n",
					errno,strerror(errno));
			exit(0);
		}
	}
	int pid_sample=fork();
	if(!pid_sample){
		if(0<=submission.language&&submission.language<6)
			sprintf(path_execute,"./main.out");
		else if(6<=submission.language&&submission.language<7)
			sprintf(path_execute,"java main");
		ptrace(PTRACE_TRACEME,pid_sample,NULL,NULL);
		chdir(path_testarea);
		close(fd_sample_to_rater[0]);
		close(fd_rater_to_sample[1]);
		close(0);
		dup(fd_rater_to_sample[0]);
		close(1);
		dup(fd_sample_to_rater[1]);
		setuid(config.uid_acoj_executer);
		limit_sample_resource(submission,testdata);
		execl(path_execute,path_execute,NULL);
	}
	close(fd_rater_to_sample[0]);
	close(fd_rater_to_sample[1]);
	close(fd_sample_to_rater[0]);
	close(fd_sample_to_rater[1]);
	return pair<int,int>(pid_sample,pid_rater);
}
