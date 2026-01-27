#ifndef VALIDATION_H
#define VALIDATION_H

#include "consumer.h"

int validate_name(const char *name);
int validate_phone(const char *phone);
int validate_service_number(const char *service_num, char service_numbers[][MAX_SERVICE_LEN], int count);
int validate_readings(float prev, float curr);
int validate_consumer_data(const Consumer *consumer, char service_numbers[][MAX_SERVICE_LEN], int count);

#endif

