/*
   acoj kernel
   ./config.hpp
 */
#include<sys/stat.h>
#include<fstream>
class config_s{
	public:
		string path_config;
		string path_testdata,path_testarea;
		string mysql_host,mysql_username,mysql_password,mysql_database;
		int count_cores;
		int uid_acoj_rater;
		int uid_acoj_executer;
		config_s(string path_config)
			:path_config(path_config){}
		void ensure_dir_exists(string);
		void ensure_paths_exist();
		void load();
};
void config_s::load(){
	ifstream fs;
	fs.open(path_config);
	fs>>path_testdata>>path_testarea;
	fs>>mysql_host>>mysql_username>>mysql_password>>mysql_database;
	fs>>count_cores;
	fs>>uid_acoj_rater;
	fs>>uid_acoj_executer;
	fs.close();
}
void config_s::ensure_dir_exists(string path){
	struct stat st;
	if(stat(path.c_str(),&st)<0)
		mkdir(path.c_str(),0755);
}
void config_s::ensure_paths_exist(){
	ensure_dir_exists(path_testdata);
	ensure_dir_exists(path_testarea);
}
config_s config("config.txt");
