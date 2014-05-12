/*
 * ACOJ Judge Client
 * ./compiler.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
class compilation_result_s{
	public:
		bool error;
		char message[65536];
		compilation_result_s(){}
		compilation_result_s(submission_s&submission);
		void compile_classic_compiled_language(submission_s&submission);
		void compile_java(submission_s&submission);
};
void compilation_result_s::compile_classic_compiled_language(submission_s&submission){
	static const int count_languages=6;
	static const char extensions[count_languages][16]={
		".c",
		".c",
		".c",
		".cpp",
		".cpp",
		".pas",
	};
	static const char compiling_commands[count_languages][1024]={
		"gcc -std=c90 -Ofast -lm -o %s %s 2>&1",
		"gcc -std=c99 -Ofast -lm -o %s %s 2>&1",
		"gcc -std=c11 -Ofast -lm -o %s %s 2>&1",
		"g++ -std=c++98 -Ofast -o %s %s 2>&1",
		"g++ -std=c++11 -Ofast -o %s %s 2>&1",
		"fpc -o%s %s 2>&1",
	};
	chdir(config.path_testarea.c_str());
	const int max_size_path=1024;
	char path_source[max_size_path],
	     path_execute[max_size_path],
	     compiling_command[1024];
	sprintf(path_source,"main%s",
			extensions[submission.language]);
	sprintf(path_execute,"main.out");
	sprintf(compiling_command,
			compiling_commands[submission.language],
			path_execute,
			path_source
	       );
	// write the source code into file.
	FILE*file_source=fopen(path_source,"w");
	fputs(submission.sourcecode,file_source);
	fclose(file_source);
	// call the compiler and read the stdout messages.
	FILE*pipe_compiler=popen(compiling_command,"r");
	char*last_message=message;
	*last_message=0;
	while(fgets(last_message,1024,pipe_compiler)&&
			last_message-message+1024<65536){
		last_message+=strlen(last_message);
	}
	// compilation error if the compiler does not return EXIT_SUCCESS.
	error=pclose(pipe_compiler)!=EXIT_SUCCESS;
	// remove the source file.
	remove(path_source);
	// remove the object file if language is pascal.
	if(submission.language==5){
		char path_object[max_size_path];
		sprintf(path_object,"main.o");
		remove(path_object);
	}
	chdir("../");
}
void compilation_result_s::compile_java(submission_s&submission){
	chdir(config.path_testarea.c_str());
	const int max_size_path=1024;
	char path_source[max_size_path]="main.java",
	     path_execute[max_size_path]="main.out",
	     compiling_command[1024];
	sprintf(compiling_command,"javac %s 2>&1",path_source);
	// write the source code into file.
	FILE*file_source=fopen(path_source,"w");
	fputs(submission.sourcecode,file_source);
	fclose(file_source);
	// call the compiler and read the stdout messages.
	FILE*pipe_compiler=popen(compiling_command,"r");
	char*last_message=message;
	*last_message=0;
	while(fgets(last_message,1024,pipe_compiler)&&
			last_message-message+1024<65536)
		last_message+=strlen(last_message);
	// compilation error if the compiler does not return EXIT_SUCCESS.
	error=pclose(pipe_compiler)!=EXIT_SUCCESS;
	// remove the source file.
	remove(path_source);
	chdir("../");
}
compilation_result_s::compilation_result_s(submission_s&submission){
	if(0<=submission.language&&submission.language<6){
		compile_classic_compiled_language(submission);
	}else if(6<=submission.language&&submission.language<7){
		compile_java(submission);
	}
}
