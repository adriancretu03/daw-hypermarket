-- Demo data for manual testing. Run after schema.sql on a fresh database.
-- Login credentials for these accounts are documented in README.md.

INSERT INTO users (id, name, email, password_hash, role) VALUES
    (1, 'Ana Popescu', 'admin@hypermarket.test', '$2y$10$U2TKehQSjTalqAWJUzxIoOIwULDKHLtjviDPMzZWvPCwm5bz5twAK', 'admin'),
    (2, 'Emil Ionescu', 'employee1@hypermarket.test', '$2y$10$U2TKehQSjTalqAWJUzxIoOIwULDKHLtjviDPMzZWvPCwm5bz5twAK', 'employee'),
    (3, 'Elena Georgescu', 'employee2@hypermarket.test', '$2y$10$U2TKehQSjTalqAWJUzxIoOIwULDKHLtjviDPMzZWvPCwm5bz5twAK', 'employee'),
    (4, 'Cristian Dumitrescu', 'customer1@hypermarket.test', '$2y$10$U2TKehQSjTalqAWJUzxIoOIwULDKHLtjviDPMzZWvPCwm5bz5twAK', 'customer'),
    (5, 'Carla Constantin', 'customer2@hypermarket.test', '$2y$10$U2TKehQSjTalqAWJUzxIoOIwULDKHLtjviDPMzZWvPCwm5bz5twAK', 'customer');

INSERT INTO categories (id, name) VALUES
    (1, 'Lactate'),
    (2, 'Panificatie'),
    (3, 'Bauturi'),
    (4, 'Electronice'),
    (5, 'Menaj');

INSERT INTO products (id, category_id, name, price, stock_qty, description) VALUES
    (1, 1, 'Lapte integral 1L', 5.49, 120, 'Lapte integral proaspat, 3.5% grasime.'),
    (2, 1, 'Cascaval 200g', 12.99, 60, 'Cascaval maturat, bucata de 200g.'),
    (3, 2, 'Paine alba', 4.29, 80, 'Paine alba coapta zilnic.'),
    (4, 2, 'Croissant cu unt', 3.79, 100, 'Croissant pufos, cu unt.'),
    (5, 3, 'Suc de portocale 1L', 8.99, 70, 'Suc 100% natural de portocale.'),
    (6, 3, 'Apa minerala 1.5L', 3.49, 150, 'Apa minerala carbogazoasa natural.'),
    (7, 4, 'Mouse wireless', 49.99, 40, 'Mouse ergonomic wireless cu receiver USB.'),
    (8, 4, 'Incarcator USB-C 20W', 39.99, 55, 'Incarcator rapid USB-C pentru priza.'),
    (9, 5, 'Detergent de vase 500ml', 6.99, 90, 'Detergent degresant, parfum de lamaie.'),
    (10, 5, 'Prosoape de hartie (pachet 6)', 15.99, 65, 'Prosoape de hartie absorbante, multifunctionale.');

INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
    (1, 4, 5, 'Lapte foarte bun, mereu proaspat.'),
    (1, 5, 4, 'Calitate buna, dar putin cam scump.'),
    (2, 4, 5, 'Cel mai bun cascaval gasit aici.'),
    (5, 5, 3, 'Suc decent, un pic prea dulce pentru mine.'),
    (7, 4, 4, 'Mouse comod, functioneaza bine.'),
    (9, 5, 5, 'Miroase excelent si curata bine vasele.');
