#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include "validation.h"

int validate_name(const char *name) {
    if (strlen(name) == 0 || strlen(name) > MAX_NAME_LEN - 1) {
        return 0;
    }
    
    for (int i = 0; name[i] != '\0'; i++) {
        if (!isalpha(name[i]) && name[i] != ' ') {
            return 0;
        }
    }
    return 1;
}

int validate_phone(const char *phone) {
    if (strlen(phone) != 10) {
        return 0;
    }
    
    for (int i = 0; i < 10; i++) {
        if (!isdigit(phone[i])) {
            return 0;
        }
    }
    return 1;
}

int validate_service_number(const char *service_num, char service_numbers[][MAX_SERVICE_LEN], int count) {
    if (strlen(service_num) == 0) {
        return 0;
    }
    
    for (int i = 0; i < count; i++) {
        if (strcmp(service_numbers[i], service_num) == 0) {
            return 0;
        }
    }
    return 1;
}

int validate_readings(float prev, float curr) {
    return curr >= prev;
}

int validate_consumer_data(const Consumer *consumer, char service_numbers[][MAX_SERVICE_LEN], int count) {
    if (!validate_name(consumer->name)) {
        printf("Error: Invalid name! Name should contain only alphabets and spaces.\n");
        return 0;
    }
    
    if (!validate_phone(consumer->phone)) {
        printf("Error: Invalid phone number! Must be exactly 10 digits.\n");
        return 0;
    }
    
    if (!validate_service_number(consumer->service_number, service_numbers, count)) {
        printf("Error: Service number already exists or is invalid!\n");
        return 0;
    }
    
    if (!validate_readings(consumer->prev_reading, consumer->curr_reading)) {
        printf("Error: Current reading must be greater than or equal to previous reading!\n");
        return 0;
    }
    
    return 1;
}

