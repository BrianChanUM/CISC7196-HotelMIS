-- Hotel Management System - Test Data
INSERT INTO hoteloutlet (OutletName, OutletSlogan, OutletMenu, `Opening Hour`, status, Style, capacity) VALUES
('Chinese Restaurant', 'Taste of East', 'Cantonese, Sichuan, Hunan', '11:00:00', 1, 'Chinese', 100),
('Western Restaurant', 'Western Elegance', 'Steak, Pasta, Salad', '12:00:00', 1, 'Western', 80),
('Japanese Restaurant', 'Fresh Sushi', 'Sushi, Sashimi, Teppanyaki', '11:30:00', 1, 'Japanese', 60),
('Coffee Shop', 'Relaxing Coffee', 'Coffee, Desserts, Sandwiches', '08:00:00', 1, 'Cafe', 40),
('Lobby Bar', 'Elegant Drinks', 'Cocktails, Wine, Snacks', '18:00:00', 1, 'Bar', 50);

INSERT INTO hotelvehicletype (VehicleType, status, daily_quantity) VALUES
('Luxury Sedan', 1, 5),
('Business Van', 1, 8),
('SUV', 1, 3),
('MPV', 1, 4),
('Luxury Bus', 1, 2);

INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, NoofGuest, OrderRemark, Status, isRequired, AssignedTo) VALUES
('Dining', '2026-05-01 12:30:00', 88001, 'guest1@example.com', 4, 'Chinese Restaurant - Family Dinner', 'Completed', 1, 'staff1'),
('Dining', '2026-05-01 19:00:00', 88002, 'guest2@example.com', 2, 'Western Restaurant - Couple Dinner', 'Completed', 1, 'staff2'),
('Dining', '2026-05-02 12:00:00', 88003, 'guest3@example.com', 6, 'Chinese Restaurant - Business Lunch', 'Completed', 1, 'staff1'),
('Dining', '2026-05-02 18:30:00', 88004, 'guest4@example.com', 3, 'Japanese Restaurant - Friends Gathering', 'Completed', 1, 'staff3'),
('Dining', '2026-05-03 11:30:00', 88005, 'guest5@example.com', 2, 'Coffee Shop - Afternoon Tea', 'Completed', 1, 'staff4'),
('Dining', '2026-05-03 19:30:00', 88006, 'guest6@example.com', 8, 'Chinese Restaurant - Birthday Party', 'Completed', 1, 'staff1'),
('Dining', '2026-05-04 12:15:00', 88007, 'guest7@example.com', 4, 'Western Restaurant - Business Lunch', 'Completed', 1, 'staff2'),
('Dining', '2026-05-04 18:00:00', 88008, 'guest8@example.com', 2, 'Japanese Restaurant - Date', 'Completed', 1, 'staff3'),
('Dining', '2026-05-05 14:00:00', 88009, 'guest9@example.com', 5, 'Coffee Shop - Meeting Break', 'Completed', 1, 'staff4'),
('Dining', '2026-05-05 20:00:00', 88010, 'guest10@example.com', 10, 'Lobby Bar - Celebration', 'Completed', 1, 'staff5'),
('Dining', '2026-05-10 12:00:00', 88011, 'guest11@example.com', 3, 'Chinese Restaurant - Family', 'Completed', 1, 'staff1'),
('Dining', '2026-05-10 19:30:00', 88012, 'guest12@example.com', 4, 'Western Restaurant - Birthday', 'Completed', 1, 'staff2'),
('Dining', '2026-05-15 11:30:00', 88013, 'guest13@example.com', 2, 'Coffee Shop - Breakfast', 'Completed', 1, 'staff4'),
('Dining', '2026-05-15 18:45:00', 88014, 'guest14@example.com', 6, 'Japanese Restaurant - Team Dinner', 'Completed', 1, 'staff3'),
('Dining', '2026-05-20 12:30:00', 88015, 'guest15@example.com', 2, 'Lobby Bar - Drinks', 'Completed', 1, 'staff5'),
('F&B', '2026-05-21 15:00:00', 88016, 'guest16@example.com', 10, 'Coffee Shop - Afternoon Tea Set', 'Completed', 1, 'staff4'),
('F&B', '2026-05-22 21:00:00', 88017, 'guest17@example.com', 8, 'Lobby Bar - Cocktail Party', 'Completed', 1, 'staff5'),
('F&B', '2026-05-25 14:30:00', 88018, 'guest18@example.com', 4, 'Coffee Shop - Business Tea', 'Completed', 1, 'staff4'),
('Dining', '2026-05-28 12:00:00', 88019, 'guest19@example.com', 12, 'Chinese Restaurant - Wedding', 'Completed', 1, 'staff1'),
('Dining', '2026-05-30 19:00:00', 88020, 'guest20@example.com', 2, 'Japanese Restaurant - Anniversary', 'Completed', 1, 'staff3');

INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, NoofGuest, OrderRemark, Status, isRequired, AssignedTo) VALUES
('Limo', '2026-05-01 09:00:00', 99001, 'limo1@example.com', 3, 'Luxury Sedan - Airport Pickup', 'Completed', 1, 'driver1'),
('Limo', '2026-05-01 14:00:00', 99002, 'limo2@example.com', 6, 'Business Van - Meeting Transfer', 'Completed', 1, 'driver2'),
('Limo', '2026-05-02 10:30:00', 99003, 'limo3@example.com', 4, 'SUV - Sightseeing', 'Completed', 1, 'driver3'),
('Limo', '2026-05-02 16:00:00', 99004, 'limo4@example.com', 8, 'MPV - Group Tour', 'Completed', 1, 'driver4'),
('Limo', '2026-05-03 08:00:00', 99005, 'limo5@example.com', 15, 'Luxury Bus - Airport Transfer', 'Completed', 1, 'driver5'),
('Limo', '2026-05-05 11:00:00', 99006, 'limo6@example.com', 2, 'Luxury Sedan - Business Transfer', 'Completed', 1, 'driver1'),
('Limo', '2026-05-10 09:30:00', 99007, 'limo7@example.com', 5, 'Business Van - Client Transfer', 'Completed', 1, 'driver2'),
('Limo', '2026-05-10 15:00:00', 99008, 'limo8@example.com', 3, 'SUV - Shopping Trip', 'Completed', 1, 'driver3'),
('Limo', '2026-05-15 08:30:00', 99009, 'limo9@example.com', 12, 'Luxury Bus - Tour Group', 'Completed', 1, 'driver5'),
('Limo', '2026-05-15 17:00:00', 99010, 'limo10@example.com', 4, 'MPV - Family Trip', 'Completed', 1, 'driver4'),
('Limo', '2026-05-20 10:00:00', 99011, 'limo11@example.com', 1, 'Luxury Sedan - Personal Trip', 'Completed', 1, 'driver1'),
('Limo', '2026-05-22 14:30:00', 99012, 'limo12@example.com', 7, 'Business Van - Team Building', 'Completed', 1, 'driver2'),
('Limo', '2026-05-25 09:00:00', 99013, 'limo13@example.com', 20, 'Luxury Bus - Large Group', 'Completed', 1, 'driver5'),
('Limo', '2026-05-28 16:00:00', 99014, 'limo14@example.com', 4, 'SUV - Mountain Trip', 'Completed', 1, 'driver3'),
('Limo', '2026-05-30 11:30:00', 99015, 'limo15@example.com', 6, 'MPV - Airport Dropoff', 'Completed', 1, 'driver4');

INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, NoofGuest, OrderRemark, Status, isRequired, AssignedTo) VALUES
('Hotel', '2026-05-01 14:00:00', 77001, 'hotel1@example.com', 2, 'Deluxe King Room - 2 nights', 'Completed', 1, 'reception1'),
('Hotel', '2026-05-02 15:00:00', 77002, 'hotel2@example.com', 4, 'Family Suite - 3 nights', 'Completed', 1, 'reception2'),
('Hotel', '2026-05-05 12:00:00', 77003, 'hotel3@example.com', 1, 'Standard Room - 1 night', 'Completed', 1, 'reception1'),
('Hotel', '2026-05-10 16:00:00', 77004, 'hotel4@example.com', 3, 'Business Suite - 2 nights', 'Completed', 1, 'reception2'),
('Hotel', '2026-05-15 11:00:00', 77005, 'hotel5@example.com', 2, 'Deluxe Twin Room - 1 night', 'Completed', 1, 'reception1'),
('Hotel', '2026-05-20 14:30:00', 77006, 'hotel6@example.com', 6, 'Executive Suite - 4 nights', 'Completed', 1, 'reception2'),
('Hotel', '2026-05-25 10:00:00', 77007, 'hotel7@example.com', 2, 'Standard Room - 2 nights', 'Completed', 1, 'reception1'),
('Hotel', '2026-05-30 17:00:00', 77008, 'hotel8@example.com', 4, 'Family Suite - 5 nights', 'Completed', 1, 'reception2');

SELECT 'Test data inserted successfully!' AS Result;
