#ifndef CONSUMER_H
#define CONSUMER_H

#define MAX_NAME_LEN 100
#define MAX_PHONE_LEN 11
#define MAX_SERVICE_LEN 20
#define MAX_CATEGORY_LEN 20
#define MAX_DATE_LEN 11

typedef struct {
    char name[MAX_NAME_LEN];
    char phone[MAX_PHONE_LEN];
    char service_number[MAX_SERVICE_LEN];
    char category[MAX_CATEGORY_LEN];
    float prev_reading;
    float curr_reading;
    float units;
    float amount;
    float fine;
    float total_amount;
    char bill_date[MAX_DATE_LEN];
    char due_date[MAX_DATE_LEN];
} Consumer;

void init_consumer(Consumer *c);
void display_consumer_info(const Consumer *c);

#endif

