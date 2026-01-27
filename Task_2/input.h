#ifndef INPUT_H
#define INPUT_H

#include "consumer.h"

void get_consumer_details(Consumer *consumer);
int get_service_numbers(char service_numbers[][MAX_SERVICE_LEN], int *count);
void add_service_number(const char *service_num, char service_numbers[][MAX_SERVICE_LEN], int *count);
float get_float_input(const char *prompt);
void get_string_input(const char *prompt, char *buffer, int max_len);

#endif

