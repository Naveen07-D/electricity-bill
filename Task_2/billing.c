#include <stdio.h>
#include <string.h>
#include "billing.h"

float calculate_bill_amount(float units, const char *category) {
    if (units == 0) {
        return 25.0;
    }
    
    float rate1, rate2, rate3, rate4;
    
    if (strcmp(category, "Domestic") == 0) {
        rate1 = 1.5; rate2 = 2.5; rate3 = 3.5; rate4 = 4.5;
    } else if (strcmp(category, "Commercial") == 0) {
        rate1 = 2.5; rate2 = 3.5; rate3 = 4.5; rate4 = 5.5;
    } else {
        rate1 = 3.5; rate2 = 4.5; rate3 = 5.5; rate4 = 6.5;
    }
    
    float amount = 0.0;
    float remaining_units = units;
    
    if (remaining_units > 150) {
        amount += (remaining_units - 150) * rate4;
        remaining_units = 150;
    }
    
    if (remaining_units > 100) {
        amount += (remaining_units - 100) * rate3;
        remaining_units = 100;
    }
    
    if (remaining_units > 50) {
        amount += (remaining_units - 50) * rate2;
        remaining_units = 50;
    }
    
    amount += remaining_units * rate1;
    
    return amount;
}

void calculate_fine_and_total(Consumer *consumer, int is_overdue) {
    consumer->fine = is_overdue ? 150.0 : 0.0;
    consumer->total_amount = consumer->amount + consumer->fine;
}

void process_billing(Consumer *consumer) {
    consumer->amount = calculate_bill_amount(consumer->units, consumer->category);
    consumer->fine = 150.0;
    consumer->total_amount = consumer->amount + consumer->fine;
}

