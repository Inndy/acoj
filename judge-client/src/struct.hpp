/*
   acoj kernel
   ./struct.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
enum result_e{
	runtime_error_=0,
	security_error_=1,
	time_limit_exceed_=2,
	memory_limit_exceed_=3,
	wrong_answer_=4,
	accepted_=5
};
#include<vector>
const int max_length_sourcecode=1<<16;
struct problem_s{
	int id;
	int id_judge;
	vector<int>list_testdata;
};
struct submission_s{
	int id;
	int id_problem;
	int language;
	char sourcecode[max_length_sourcecode];
};
struct testdata_s{
	int id;
	int limit_time;
	int limit_memory;
	int limit_stack;
};
struct rater_s{
	int id;
	bool interactive;
	char sourcecode[max_length_sourcecode];
};
