#ifndef BILLING_H
#define BILLING_H

#include "consumer.h"

float calculate_bill_amount(float units, const char *category);
void calculate_fine_and_total(Consumer *consumer, int is_overdue);
void process_billing(Consumer *consumer);

#endif

