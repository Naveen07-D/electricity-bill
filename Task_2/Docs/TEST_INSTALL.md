# **Test Plan & Installation Guide**

## **Electricity Bill Management System (C)**

---

## **Installation**

### Compile (One Line)

```bash
gcc main.c consumer.c input.c validation.c billing.c output.c utils.c -o electricity_bill
```

### Run

```bash
./electricity_bill      (Linux/macOS)
electricity_bill.exe    (Windows)
```

---

## **Files Required**

* main.c
* consumer.c / consumer.h
* input.c / input.h
* validation.c / validation.h
* billing.c / billing.h
* output.c / output.h
* utils.c / utils.h

---

## **Test Plan**

### 1. Input Validation Tests

| Test                 | Input           | Expected Result |
| -------------------- | --------------- | --------------- |
| Name with numbers    | Naveen123       | Error           |
| Phone < 10 digits    | 12345           | Error           |
| Duplicate service no | Existing number | Error           |
| Current < Previous   | 150, 100        | Error           |
| Valid inputs         | Correct data    | Accepted        |

---

### 2. Billing Tests

| Category   | Units | Expected Bill |
| ---------- | ----- | ------------- |
| Domestic   | 0     | ₹25           |
| Domestic   | 75    | ₹162.5        |
| Domestic   | 200   | ₹850          |
| Commercial | 30    | ₹75           |
| Industrial | 30    | ₹105          |

---

### 3. Program Flow Test

1. Enter valid details → Bill displayed
2. Choose save → File created
3. Run again → Old service numbers detected

---

### 4. Date Test

| Bill Date  | Due Date   |
| ---------- | ---------- |
| 01-01-2024 | 16-01-2024 |
| 31-01-2024 | 15-02-2024 |

---

## **Expected Output Files**

* bills_history.txt → Stores bills
* service_numbers.dat → Stores service numbers

---

## **Common Errors**

| Problem             | Solution                |
| ------------------- | ----------------------- |
| gcc not found       | Install GCC             |
| Program not running | Check compilation       |
| File error          | Check folder permission |

---

## **Success Criteria**

* Program runs without crash
* Validations work correctly
* Correct bill calculation
* Files created successfully

---

This is **clean, short, and perfect for Word submission**.
