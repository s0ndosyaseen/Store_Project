-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2026 at 02:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `albousala`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
(12, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 12, 1, '2026-05-08 00:02:32'),
(13, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 11, 3, '2026-05-08 00:02:40'),
(14, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 13, 2, '2026-05-08 00:02:47'),
(15, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 15, 2, '2026-05-08 00:02:53'),
(16, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 14, 4, '2026-05-08 00:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL DEFAULT 50.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` enum('andalus','sham','victory','egypt') NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subcategory` enum('ديكور','اكسسوارات','ملابس') NOT NULL DEFAULT 'ديكور'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`, `subcategory`) VALUES
(1, 'طقم أواني خزف أندلسي', 'طقم فاخر مصنوع من السيراميك عالي الجودة، مزين بنقوش هندسية مستوحاة من قصر الحمراء، يضيف لمسة ملكية على مائدتك.', 850.00, 'images/item1.png', 'andalus', 20, '2026-05-07 11:08:26', 'ديكور'),
(2, 'وشاح حرير مزخرف', 'وشاح من الحرير الطبيعي الناعم، يتميز بألوان زاهية وزخارف نباتية دقيقة، مثالي لإطلالة راقية وعصرية بلمسة تراثية.', 1200.00, 'images/item2.png', 'andalus', 15, '2026-05-07 11:08:26', 'اكسسوارات'),
(3, 'مصباح نحاسي تراثي', 'قطعة فنية مشغولة يدوياً من النحاس الخالص، تعكس ظلالاً ساحرة وتضفي أجواءً من الدفء والأصالة على زوايا منزلك.', 450.00, 'images/item3.png', 'andalus', 30, '2026-05-07 11:08:26', 'ديكور'),
(4, 'طقم أندلوسي فاخر جدًا', 'سخة حصرية وراقية بتفاصيل مذهبة دقيقة، مصمم خصيصاً للمناسبات الكبرى ولمحبي الاقتناء المتميز.', 3200.00, 'images/item4.png', 'andalus', 8, '2026-05-07 11:08:26', 'ملابس'),
(5, 'سجادة صلاة تقليدية', 'سجادة صلاة مريحة مبطنة، محاكة بخيوط متينة وتصميم كلاسيكي يجمع بين البساطة والروحانية.', 980.00, 'images/item5.png', 'andalus', 12, '2026-05-07 11:08:26', 'ديكور'),
(6, 'ثوب ملكي من حيفا - مخمل أسود', 'تطريز يدوي أصيل مع خيوط القصب الذهبية، مستوحى من الزي الشامي التقليدي', 4500.00, 'images/shami.png', 'sham', 5, '2026-05-07 11:08:26', 'ملابس'),
(7, 'صندوق عرائس دمشقي', 'صندوق خشبي بتطعيم الصدف والعاج، من صنع الحرفيين الدمشقيين', 2800.00, 'images/item6.png', 'sham', 7, '2026-05-07 11:08:26', 'ديكور'),
(8, 'مشربية خشبية', 'مشربية (نافذة شرقية) مصنوعة يدوياً من خشب الجوز بنقوش هندسية', 2500.00, 'images/item7.png', 'sham', 4, '2026-05-07 11:08:26', 'ديكور'),
(9, 'شال دمشقي حريري', 'شال من الحرير الطبيعي بزخارف دمشقية أصيلة', 1500.00, 'images/item8.png', 'sham', 18, '2026-05-07 11:08:26', 'ملابس'),
(10, 'إبريق نحاس شامي', 'إبريق قهوة من النحاس المطروق بزخارف شامية تقليدية', 750.00, 'images/item9.png', 'sham', 22, '2026-05-07 11:08:26', 'ديكور'),
(11, 'ساعة جدارية فيكتورية', 'ساعة جدارية أنتيك بإطار خشبي منحوت على الطراز الفيكتوري', 3600.00, 'images/item11.png', 'victory', 6, '2026-05-07 11:08:26', 'ديكور'),
(12, 'مجموعة شمعدانات برونزية', 'طقم من 3 شمعدانات برونزية فيكتورية بتصاميم ملكية', 1800.00, 'images/item12.png', 'victory', 10, '2026-05-07 11:08:26', 'ديكور'),
(13, 'كرسي بذراعين فيكتوري', 'كرسي أنتيك بمسند يدين ومقعد منجد بالمخمل الأحمر الملكي', 7200.00, 'images/item13.png', 'victory', 3, '2026-05-07 11:08:26', 'ديكور'),
(14, 'إطار صور فضي فيكتوري', 'إطار صور مزخرف من الفضة الإنجليزية بنقوش ورود وأوراق', 950.00, 'images/item14.png', 'victory', 16, '2026-05-07 11:08:26', 'ديكور'),
(15, 'مكتبة فيكتورية خشبية', 'مكتبة صغيرة من خشب الماهوجني بزجاج مستودع وحافة منقوشة', 9500.00, 'images/item15.png', 'victory', 2, '2026-05-07 11:08:26', 'ديكور'),
(16, 'تمثال أنوبيس ذهبي', 'تمثال راتنج للإله أنوبيس مطلي بالذهب، يجسد حارس العالم الآخر', 1100.00, 'images/item16.png', 'egypt', 14, '2026-05-07 11:08:26', 'ديكور'),
(17, 'لوحة هيروغليفية', 'لوحة من الحجر الطبيعي محفورة عليها نقوش هيروغليفية أصيلة', 2200.00, 'images/item17.png', 'egypt', 9, '2026-05-07 11:08:26', 'ديكور'),
(18, 'قلادة التعويذة الفرعونية', 'قلادة ذهب عيار 18 بتصميم تعويذة عين حورس الأصيلة', 3800.00, 'images/item18.png', 'egypt', 11, '2026-05-07 11:08:26', 'ديكور'),
(19, 'صندوق كنوز فرعوني', 'صندوق خشبي بزخارف فرعونية مطلية بالذهب والأزرق الملكي', 1700.00, 'images/item19.png', 'egypt', 7, '2026-05-07 11:08:26', 'ديكور'),
(20, 'تمثال رمسيس الصغير', 'تمثال طيني مشكل يدوياً يجسد الفرعون رمسيس الثاني', 2900.00, 'images/item21.png', 'egypt', 5, '2026-05-07 11:08:26', 'ديكور'),
(21, 'صندوق مجوهرات فاخر', 'صندوق خشبي مطعم بالصدف أو النحاس، مبطن من الداخل بالمخمل لحفظ مقتنياتك الثمينة بأناقة وأمان.', 980.00, 'images/item6.png', 'andalus', 14, '2026-05-07 11:08:26', 'ديكور'),
(22, 'سجادة أندلوسية غرزة قرطبة', 'سجادة حائط أو أرضية تعتمد \"غرزة قرطبة\" الشهيرة، تحكي تفاصيلها قصة الفن العريق في الأندلس بألوان دافئة.', 1200.00, 'images/item7.png', 'andalus', 20, '2026-05-07 11:08:26', 'ديكور'),
(23, 'قفطان أندلوسي أخضر مذهب', 'رداء تقليدي من المخمل الأخضر الفاخر، مطرز بخيوط القصب الذهبية، يجسد الفخامة الأندلسية في أبهى صورها.', 3200.00, 'images/item8.png', 'andalus', 31, '2026-05-07 11:08:26', 'ملابس'),
(24, 'خاتم أندلوسي مرصع بالياقوت الأخضر', 'خاتم من الفضة الإسترلينية بتصميم \"أرابيسك\" يدوي، يتوسطه حجر ياقوت أخضر جذاب يخطف الأنظار.', 300.00, 'images/item9.png', 'andalus', 27, '2026-05-07 11:08:26', 'اكسسوارات');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'sondos', 'sondos.yaseen@gmail.com', '$2y$12$flnU8wOvlFAuBpXQUcOZo.Flx38kzrDHuT7Ek/GiLdc5vvyHjIJDG', '2026-05-07 09:06:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
