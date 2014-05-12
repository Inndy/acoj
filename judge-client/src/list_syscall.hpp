/*
 * ACOJ Judge Client
 * ./list_syscall.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
void init_syscall(int*limit_syscall){
	/*
		%eax	Name		Source
		1	sys_exit	kernel/exit.c
		5	sys_open	fs/open.c
		6	sys_close	fs/open.c
		12	sys_chdir	fs/open.c
		file: 5,6.
	*/
#ifdef __i386__
	// x86 (C/C++)
	limit_syscall[SYS_exit]=1;			//1
	limit_syscall[SYS_fork]=0;			//2
	limit_syscall[SYS_read]=-1;			//3
	limit_syscall[SYS_write]=-1;			//4
	limit_syscall[SYS_open]=-1;			//5
	limit_syscall[SYS_close]=-1;			//6
	limit_syscall[SYS_time]=-1;			//13
	limit_syscall[SYS_restart_syscall]=-1;
	limit_syscall[SYS_execve]=1;
	limit_syscall[SYS_lseek]=-1;
	limit_syscall[SYS_stime]=-1;
	limit_syscall[SYS_alarm]=-1;
	limit_syscall[SYS_oldfstat]=-1;
	limit_syscall[SYS_access]=-1;
	limit_syscall[SYS_times]=-1;
	limit_syscall[SYS_brk]=-1;
	limit_syscall[SYS_oldolduname]=-1;
	limit_syscall[SYS_ustat]=-1;
	limit_syscall[SYS_getrlimit]=-1;
	limit_syscall[SYS_getrusage]=-1;
	limit_syscall[SYS_gettimeofday]=-1;
	limit_syscall[SYS_oldstat]=-1;
	limit_syscall[SYS_readlink]=-1;
	limit_syscall[SYS_mmap]=-1;
	limit_syscall[SYS_munmap]=-1;
	limit_syscall[SYS_truncate]=-1;
	limit_syscall[SYS_ftruncate]=-1;
	limit_syscall[SYS_getpriority]=-1;
	limit_syscall[SYS_statfs]=-1;
	limit_syscall[SYS_fstatfs]=-1;
	limit_syscall[SYS_stat]=-1;
	limit_syscall[SYS_lstat]=-1;
	limit_syscall[SYS_fstat]=-1;
	limit_syscall[SYS_olduname]=-1;
	limit_syscall[SYS_uname]=-1;
	limit_syscall[SYS_mprotect]=-1;
	limit_syscall[SYS_mlock]=-1;
	limit_syscall[SYS_munlock]=-1;
	limit_syscall[SYS_mlockall]=-1;
	limit_syscall[SYS_munlockall]=-1;
	limit_syscall[SYS_mremap]=-1;
	limit_syscall[SYS_pread64]=-1;
	limit_syscall[SYS_pwrite64]=-1;
	limit_syscall[SYS_mmap2]=-1;
	limit_syscall[SYS_truncate64]=-1;
	limit_syscall[SYS_ftruncate64]=-1;
	limit_syscall[SYS_stat64]=-1;
	limit_syscall[SYS_lstat64]=-1;
	limit_syscall[SYS_fstat64]=-1;
	limit_syscall[SYS_statfs64]=-1;
	limit_syscall[SYS_set_thread_area]=2;
	limit_syscall[SYS_get_thread_area]=-1;
	limit_syscall[SYS_exit_group]=1;
	limit_syscall[SYS_fstatfs64]=-1;
	limit_syscall[SYS_fstatat64]=-1;
	// x86 (5. Free Pascal 2.6.2-5)
	limit_syscall[11]=-1;
	limit_syscall[54]=-1;
	limit_syscall[174]=-1;
	limit_syscall[191]=-1;
	// x86 (6. Java 1.7.0_25)
	limit_syscall[75]=-1;
	limit_syscall[116]=-1;
	limit_syscall[120]=-1;
	limit_syscall[140]=-1;
	limit_syscall[141]=-1;
	limit_syscall[175]=-1;
	limit_syscall[183]=-1;
	limit_syscall[201]=-1;
	limit_syscall[220]=-1;
	limit_syscall[224]=-1;
	limit_syscall[240]=-1;
	limit_syscall[258]=-1;
	limit_syscall[265]=-1;
	limit_syscall[266]=-1;
	limit_syscall[295]=-1;
	limit_syscall[311]=-1;
#elif defined(__x86_64__)
	// x86-64 (C/C++)
	limit_syscall[SYS_exit]=1;			//1
	limit_syscall[SYS_fork]=0;			//2
	limit_syscall[SYS_read]=-1;			//3
	limit_syscall[SYS_write]=-1;			//4
	limit_syscall[SYS_open]=-1;			//5
	limit_syscall[SYS_close]=-1;			//6
	limit_syscall[SYS_time]=-1;			//13
	limit_syscall[SYS_restart_syscall]=-1;
	limit_syscall[SYS_execve]=1;
	limit_syscall[SYS_lseek]=-1;
	limit_syscall[SYS_alarm]=-1;
	limit_syscall[SYS_access]=-1;
	limit_syscall[SYS_times]=-1;
	limit_syscall[SYS_brk]=-1;
	limit_syscall[SYS_ustat]=-1;
	limit_syscall[SYS_getrlimit]=-1;
	limit_syscall[SYS_getrusage]=-1;
	limit_syscall[SYS_gettimeofday]=-1;
	limit_syscall[SYS_readlink]=-1;
	limit_syscall[SYS_mmap]=-1;
	limit_syscall[SYS_munmap]=-1;
	limit_syscall[SYS_truncate]=-1;
	limit_syscall[SYS_ftruncate]=-1;
	limit_syscall[SYS_getpriority]=-1;
	limit_syscall[SYS_statfs]=-1;
	limit_syscall[SYS_fstatfs]=-1;
	limit_syscall[SYS_stat]=-1;
	limit_syscall[SYS_lstat]=-1;
	limit_syscall[SYS_fstat]=-1;
	limit_syscall[SYS_uname]=-1;
	limit_syscall[SYS_mprotect]=-1;
	limit_syscall[SYS_mlock]=-1;
	limit_syscall[SYS_munlock]=-1;
	limit_syscall[SYS_mlockall]=-1;
	limit_syscall[SYS_munlockall]=-1;
	limit_syscall[SYS_mremap]=-1;
	limit_syscall[SYS_pread64]=-1;
	limit_syscall[SYS_pwrite64]=-1;
	limit_syscall[SYS_set_thread_area]=2;
	limit_syscall[SYS_get_thread_area]=-1;
	limit_syscall[SYS_exit_group]=1;
	limit_syscall[158]=-1;
	// x86-64 (5. Free Pascal 2.6.2-5)
	limit_syscall[13]=-1;
	limit_syscall[16]=-1;
#endif
}
