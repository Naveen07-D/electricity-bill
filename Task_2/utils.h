#ifndef UTILS_H
#define UTILS_H

#include <time.h>
#include <stdio.h>

void clear_screen();
void get_current_date(char *date_buffer);
void calculate_due_date(const char *bill_date, char *due_date);
void print_header(const char *title);
void print_separator();
int is_file_empty(FILE *file);

#endif

