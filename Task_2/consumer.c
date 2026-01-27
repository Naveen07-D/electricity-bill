#include <stdio.h>
#include <string.h>
#include "consumer.h"

void init_consumer(Consumer *c) {
    memset(c->name, 0, MAX_NAME_LEN);
    memset(c->phone, 0, MAX_PHONE_LEN);
    memset(c->service_number, 0, MAX_SERVICE_LEN);
    memset(c->category, 0, MAX_CATEGORY_LEN);
    memset(c->bill_date, 0, MAX_DATE_LEN);
    memset(c->due_date, 0, MAX_DATE_LEN);
    c->prev_reading = 0.0;
    c->curr_reading = 0.0;
    c->units = 0.0;
    c->amount = 0.0;
    c->fine = 0.0;
    c->total_amount = 0.0;
}

void display_consumer_info(const Consumer *c) {
    printf("\nConsumer Information:\n");
    printf("Name: %s\n", c->name);
    printf("Phone: %s\n", c->phone);
    printf("Service Number: %s\n", c->service_number);
    printf("Category: %s\n", c->category);
}

