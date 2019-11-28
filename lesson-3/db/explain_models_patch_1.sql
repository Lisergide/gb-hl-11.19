ALTER TABLE customers
    ADD PRIMARY KEY (customerNumber);
ALTER TABLE employees
    ADD PRIMARY KEY (employeeNumber);
ALTER TABLE offices
    ADD PRIMARY KEY (officeCode);
ALTER TABLE orderdetails
    ADD PRIMARY KEY (orderNumber, productCode);
ALTER TABLE orders
    ADD PRIMARY KEY (orderNumber),
    ADD KEY (customerNumber);
ALTER TABLE payments
    ADD PRIMARY KEY (customerNumber, checkNumber);
ALTER TABLE productlines
    ADD PRIMARY KEY (productLine);
ALTER TABLE products 
    ADD PRIMARY KEY (productCode),
    ADD KEY (buyPrice),
    ADD KEY (productLine);
ALTER TABLE productvariants 
    ADD PRIMARY KEY (variantId),
    ADD KEY (buyPrice),
    ADD KEY (productCode)