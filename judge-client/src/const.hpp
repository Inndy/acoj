/*
 * ACOJ Judge Client
 * ./const.hpp
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
enum status_e{
	WJ=0,	//	Waiting for Judge
	CE=1,	//	Compilation Error
	SE=2,	//	Permission Denied
	RE=3,	//	Runtime Error
	TLE=4,	//	Time Limit Exceed
	MLE=5,	//	Memory Limit Exceed
	WA=6,	//	Wrong Answer
	AC=7	// 	Accepted
};
char name_status[8][32]={
	"waiting for judge",
	"compilation error",
	"permission denied",
	"runtime error",
	"time limit exceed",
	"memory limit exceed",
	"wrong answer",
	"accepted",
};
char name_status_short[8][32]={
	"WJ",
	"CE",
	"PD",
	"RE",
	"TLE",
	"MLE",
	"WA",
	"AC",
};
status_e result_to_status[]={
	RE,
	SE,
	TLE,
	MLE,
	WA,
	AC
};
