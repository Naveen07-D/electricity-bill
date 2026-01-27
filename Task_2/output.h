#ifndef OUTPUT_H
#define OUTPUT_H

#include "consumer.h"

void display_bill(const Consumer *consumer);
void display_summary(const Consumer *consumer);
void save_bill_to_file(const Consumer *consumer);

#endif

