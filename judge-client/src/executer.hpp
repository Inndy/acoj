/*
 * ACOJ Judge Client
 * ./executer.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
#include<sys/ptrace.h>
#include<sys/reg.h>
#include<sys/syscall.h>
#include<sys/types.h>
#include<sys/user.h>
#include<sys/wait.h>
#include"list_syscall.hpp"
#include"executer_child.hpp"
int check_syscall(
		int pid,
		int*limit_syscall
		){
	user_regs_struct regs;
	ptrace(PTRACE_GETREGS,pid,NULL,&regs);
#ifdef __i386__
	int syscall=regs.orig_eax;
#elif defined(__x86_64__)
	int syscall=regs.orig_rax;
#endif
	if(limit_syscall[syscall]<0){		// valid
	}else if(limit_syscall[syscall]==0){	// invalid
		return syscall;
	}else if(limit_syscall[syscall]>0){	// limited
		limit_syscall[syscall]--;
	}
	return 0;
}
int calc_usage_time_ms(rusage rinfo){	// ms
	return (rinfo.ru_utime.tv_sec+rinfo.ru_stime.tv_sec)*1000
		+(rinfo.ru_utime.tv_usec+rinfo.ru_stime.tv_usec)/1000;
}
long long int calc_usage_memory(int pid){	// Byte
	char path[256];
	sprintf(path,"/proc/%i/statm",pid);
	FILE*f=fopen(path,"r");
	long long int r;
	for(int i=0;i<6;i++)
		fscanf(f,"%lli",&r);
	fclose(f);
	return r*getpagesize();
}
int pid_for_timer;
void timer(int sig){
	kill(pid_for_timer,SIGUSR1);
}
enum execution_error_e{
	none=0,				// 0 if none
	illegal_instruction=1,		// 1 if illegal instruction
	float_point_exception=2,	// 2 if floating point exception
	invalid_memory_reference=3	// 3 if invalid memory reference
};
class execution_result_s{
	public:
		execution_error_e error;
		int invalid_systemcall;
		int usage_time_ms;
		int usage_memory_kib;
		result_e result;
		execution_result_s(){}
		execution_result_s(
				submission_s&submission,
				testdata_s&testdata,
				rater_s&rater
				);
};
execution_result_s::execution_result_s(
		submission_s&submission,
		testdata_s&testdata,
		rater_s&rater
		){
	// default value
	error=none;
	invalid_systemcall=0;
	result=accepted_;
	// default end
	setaffinity(0);
	int pid,pid_rater;
	if(rater.interactive){
		pair<int,int>r;
		r=execute_child_process_interactive(submission,testdata);
		pid=r.first;
		pid_rater=r.second;
	}else{
		pid=execute_child_process(submission,testdata);
	}
	char path_execute[1024];
	sprintf(path_execute,"%smain.out",
			config.path_testarea.c_str()
	       );
	pid_for_timer=pid;
	// define what to do when an alarm is recieved.
	signal(SIGALRM,timer);
	// end define
	int limit_syscall[349];
	init_syscall(limit_syscall);
	int previous_state=0;
	bool alarm_scheduled=0;
	while(1){
		int state=0;
		const int max_message=1e3;
		char message[max_message];
		int status;
		rusage rinfo;
		wait4(pid,&status,0,&rinfo);
		// schedule the alarm.
		if(!alarm_scheduled){
			ualarm(1000,1000);
			alarm_scheduled=1;
		}
		if(WIFEXITED(status)){
			state=1;
#ifdef ACOJ_DEBUG
			sprintf(message,"child process exited.");
#endif
			break;
		}else if(WIFSTOPPED(status)){
			state=2;
			int sig=WSTOPSIG(status);
			if(sig==SIGILL){
				state=3;
#ifdef ACOJ_DEBUG
				sprintf(message,"sig=%i, SIGILL(int(4)): Illegal Instruction.",sig);
#endif
				error=illegal_instruction;
				result=runtime_error_;
			}else if(sig==SIGTRAP){
				state=4;
#ifdef ACOJ_DEBUG
				sprintf(message,"sig=%i, SIGTRAP(int(5)): Trace/breakpoint trap.",sig);
#endif
				invalid_systemcall=check_syscall(pid,limit_syscall);
				if(invalid_systemcall)
					result=security_error_;
			}else if(sig==SIGFPE){
				state=5;
#ifdef ACOJ_DEBUG
				sprintf(message,"sig=%i, SIGFPE(int(8)): Floating point exception.",sig);
#endif
				error=float_point_exception;
				result=runtime_error_;
			}else if(sig==SIGUSR1){
				state=6;
#ifdef ACOJ_DEBUG
				sprintf(message,"sig=%i, SIGUSR1(int(10)): User-defined signal 1.",sig);
#endif
			}else if(sig==SIGSEGV){
				state=7;
#ifdef ACOJ_DEBUG
				sprintf(message,"sig=%i, SIGSEGV(int(11)): Invalid memory reference.",sig);
#endif
				error=invalid_memory_reference;
				result=runtime_error_;
			}else{
				state=8;
#ifdef ACOJ_DEBUG
				sprintf(message,"stopped, signal: %i",sig);
#endif
			}
		}else if(WIFSIGNALED(status)){
			state=9;
#ifdef ACOJ_DEBUG
			sprintf(message,"runtime error, recived signal: %i.",WTERMSIG(stat));
#endif
			result=runtime_error_;
		}
		usage_time_ms=calc_usage_time_ms(rinfo);
		usage_memory_kib=(int)(calc_usage_memory(pid)/1000);
		if(submission.language==6){
			usage_memory_kib-=727400;
		}
#ifdef ACOJ_DEBUG
		if(state!=previous_state)
			printf("\n\t%s\n",message);
		printf("\n\truntime: %ims, memory: %iKiB",
				usage_time_ms,
				usage_memory_kib
		      );
		fflush(stdout);
#endif
		if(usage_time_ms>=testdata.limit_time)
			result=time_limit_exceed_;
		else if(usage_memory_kib>=testdata.limit_memory)
			result=memory_limit_exceed_;
		if(result!=accepted_)
			break;
		ptrace(PTRACE_SYSCALL,pid,NULL,NULL);
		previous_state=state;
	}
	// terminate the alarm.
	alarm(0);
#ifdef ACOJ_DEBUG
	printf("\n\n");
	printf("\tstatus: %s.\n",
			name_status[result_to_status[result]]);
#endif
	if(result!=accepted_){
		ptrace(PTRACE_KILL,pid,0,0);
		waitpid(pid,0,0);
	}
	if(rater.interactive){
		usleep(100*1000);
		int r=kill(pid_rater,SIGUSR1);
		if(r==0)
			waitpid(pid_rater,0,0);
	}
#ifdef ACOJ_DEBUG
	printf("\n");
#endif
	// remove the stderr file.
	char path_stderr[1024];
	sprintf(path_stderr,"%sstderr.txt",config.path_testarea.c_str());
	remove(path_stderr);
}
