/*
   acoj kernel
   ./rater.hpp
 */
#include<cmath>
float rate(
		testdata_s&testdata,
		rater_s&rater
	){
	float return_value=-INFINITY;
	static const int max_path=1<<10;
	char path_rating[max_path];
	sprintf(path_rating,
			"%srating.txt",
			config.path_testarea.c_str());
	if(!rater.interactive){
		char path_submission_output[max_path];
		sprintf(path_submission_output,"%soutput.txt",
				config.path_testarea.c_str()
		       );
		// execute the rater
		int pid_child=fork();
		if(!pid_child){
			char path_rater[max_path];
			char path_testdata_input[max_path];
			char path_testdata_output[max_path];
			sprintf(path_rater,"%srater.out",
					config.path_testarea.c_str()
			       );
			sprintf(path_testdata_input,"%s%i.in",
					config.path_testdata.c_str(),
					testdata.id
			       );
			sprintf(path_testdata_output,"%s%i.out",
					config.path_testdata.c_str(),
					testdata.id
			       );
			freopen(path_rating,"w",stdout);
			int r;
			r=execl(
					path_rater,
					path_rater,
					path_testdata_input,
					path_testdata_output,
					path_submission_output,
					NULL
			     );
			if(r){
				freopen("./error.rater.log","w",stdout);
				printf("%i: %s\nrater executing error\n",
						errno,strerror(errno));
				exit(0);
			}
		}
		waitpid(pid_child,0,0);
		remove(path_submission_output);
	}
	// read the rating
	FILE*file_rating=fopen(path_rating,"r");
	if(file_rating){
		fscanf(file_rating,"%f",&return_value);
		fclose(file_rating);
	}else{
		return_value=0;
		fprintf(stderr,"error: cannot open the rating file.\n");
	}
	remove(path_rating);
	return return_value;
}
