#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include "utils.h"

void clear_screen() {
    #ifdef _WIN32
        system("cls");
    #else
        system("clear");
    #endif
}

void get_current_date(char *date_buffer) {
    time_t t = time(NULL);
    struct tm tm = *localtime(&t);
    sprintf(date_buffer, "%02d-%02d-%04d", tm.tm_mday, tm.tm_mon + 1, tm.tm_year + 1900);
}

void calculate_due_date(const char *bill_date, char *due_date) {
    int day, month, year;
    sscanf(bill_date, "%d-%d-%d", &day, &month, &year);
    
    struct tm tm = {0};
    tm.tm_mday = day;
    tm.tm_mon = month - 1;
    tm.tm_year = year - 1900;
    
    time_t t = mktime(&tm);
    t += 15 * 24 * 60 * 60;
    
    struct tm *new_tm = localtime(&t);
    sprintf(due_date, "%02d-%02d-%04d", new_tm->tm_mday, new_tm->tm_mon + 1, new_tm->tm_year + 1900);
}

void print_header(const char *title) {
    printf("\n╔══════════════════════════════════════════════════════════╗\n");
    printf("║%*s%*s║\n", (int)(30 + strlen(title)/2), title, (int)(30 - strlen(title)/2), "");
    printf("╚══════════════════════════════════════════════════════════╝\n");
}

void print_separator() {
    printf("────────────────────────────────────────────────────────────\n");
}

int is_file_empty(FILE *file) {
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    rewind(file);
    return size == 0;
}

