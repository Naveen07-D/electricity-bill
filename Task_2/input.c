#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include "input.h"
#include "utils.h"

void get_string_input(const char *prompt, char *buffer, int max_len) {
    printf("%s", prompt);
    fgets(buffer, max_len, stdin);
    buffer[strcspn(buffer, "\n")] = 0;
}

float get_float_input(const char *prompt) {
    char input[50];
    float value;
    
    while (1) {
        printf("%s", prompt);
        fgets(input, sizeof(input), stdin);
        
        if (sscanf(input, "%f", &value) == 1) {
            return value;
        }
        printf("Invalid input! Please enter a valid number.\n");
    }
}

void get_consumer_details(Consumer *consumer) {
    print_header("Consumer Registration");
    
    get_string_input("Enter Consumer Name: ", consumer->name, MAX_NAME_LEN);
    
    while (1) {
        get_string_input("Enter Phone Number (10 digits): ", consumer->phone, MAX_PHONE_LEN);
        if (strlen(consumer->phone) == 10) {
            int valid = 1;
            for (int i = 0; i < 10; i++) {
                if (!isdigit(consumer->phone[i])) {
                    valid = 0;
                    break;
                }
            }
            if (valid) break;
        }
        printf("Invalid phone number! Must be exactly 10 digits.\n");
    }
    
    getchar(); // Consume leftover newline from phone input
    get_string_input("Enter Service Number: ", consumer->service_number, MAX_SERVICE_LEN);
    
    printf("Select Consumer Category:\n");
    printf("1. Domestic\n");
    printf("2. Commercial\n");
    printf("3. Industrial\n");
    
    int choice;
    while (1) {
        printf("Enter choice (1-3): ");
        char input[10];
        fgets(input, sizeof(input), stdin);
        if (sscanf(input, "%d", &choice) == 1 && choice >= 1 && choice <= 3) {
            break;
        }
        printf("Invalid choice! Please enter 1, 2, or 3.\n");
    }
    
    switch(choice) {
        case 1: strcpy(consumer->category, "Domestic"); break;
        case 2: strcpy(consumer->category, "Commercial"); break;
        case 3: strcpy(consumer->category, "Industrial"); break;
    }
    
    consumer->prev_reading = get_float_input("Enter Previous Meter Reading: ");
    
    while (1) {
        consumer->curr_reading = get_float_input("Enter Current Meter Reading: ");
        if (consumer->curr_reading > consumer->prev_reading) {
            break;
        }
        printf("Current reading must be greater than previous reading!\n");
    }
    
    consumer->units = consumer->curr_reading - consumer->prev_reading;
    
    get_current_date(consumer->bill_date);
    calculate_due_date(consumer->bill_date, consumer->due_date);
}

int get_service_numbers(char service_numbers[][MAX_SERVICE_LEN], int *count) {
    FILE *file = fopen("service_numbers.dat", "rb");
    *count = 0;
    
    if (file == NULL) {
        return 0;
    }
    
    char service_num[MAX_SERVICE_LEN];
    while (fread(service_num, sizeof(char), MAX_SERVICE_LEN, file) == MAX_SERVICE_LEN) {
        strcpy(service_numbers[*count], service_num);
        (*count)++;
    }
    
    fclose(file);
    return 1;
}

void add_service_number(const char *service_num, char service_numbers[][MAX_SERVICE_LEN], int *count) {
    if (*count < 100) {
        strcpy(service_numbers[*count], service_num);
        (*count)++;
        
        FILE *file = fopen("service_numbers.dat", "ab");
        if (file != NULL) {
            fwrite(service_num, sizeof(char), MAX_SERVICE_LEN, file);
            fclose(file);
        }
    }
}

