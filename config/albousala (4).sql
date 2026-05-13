-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 11:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
                            `id` int(11) NOT NULL,
                            `username` varchar(50) NOT NULL,
                            `password` varchar(255) NOT NULL,
                            `role` enum('admin','employee') DEFAULT 'employee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`id`, `username`, `password`, `role`) VALUES
                                                                  (1, 'admin', '$2y$10$XiSXDw.w40AIlwLnC6VP2.wqwqMy1Cz4OeKDWLANlyOSuhKtpRYvi', 'admin'),
                                                                  (7, 'acc2', '$2y$10$hBuOsSI4qDKuVh8.jK6ZIemRhOcBHc06h4eH8gLtfvaWIM31c1xYu', 'employee'),
                                                                  (8, 'acc1', '$2y$10$Ys30C8in58soQlRN4AOUjONTj1LPdIiYfO2G6vuEzgKSeNqHu/ZEa', 'employee');

CREATE TABLE `cart_items` (
                              `id` int(11) NOT NULL,
                              `user_id` int(11) DEFAULT NULL,
                              `session_id` varchar(100) DEFAULT NULL,
                              `product_id` int(11) NOT NULL,
                              `quantity` int(11) NOT NULL DEFAULT 1,
                              `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cart_items` (`id`, `user_id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
                                                                                                     (38, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 1, 2, '2026-05-08 15:14:55'),
                                                                                                     (39, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 2, 1, '2026-05-08 15:14:57'),
                                                                                                     (40, NULL, 'kd1e8esfh1hokmsh7h6aa9vfur', 3, 1, '2026-05-08 15:14:59');

CREATE TABLE `categories` (
                              `id` int(11) NOT NULL,
                              `slug` varchar(50) NOT NULL,
                              `title` varchar(100) NOT NULL,
                              `hero_title` varchar(200) DEFAULT NULL,
                              `hero_desc` text DEFAULT NULL,
                              `bg_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `slug`, `title`, `hero_title`, `hero_desc`, `bg_image`) VALUES
                                                                                            (1, 'andalus', 'الحضارة الأندلسية', 'عبق الأندلس يزين المكان', 'قطع فنية مستوحاة من عظمة التاريخ الأندلسي، مصنوعة يدوياً.', 'images/bgand.png'),
                                                                                            (2, 'sham', 'بلاد الشام', 'سحر الشام يكتمل بكِ', 'من نقوش الحرير إلى زخارف الياسمين، ننقل لكِ روح الحارة الدمشقية.', 'images/shami.png'),
                                                                                            (3, 'egypt', 'الحضارة الفرعونية', 'سحرُ الخلود يحيطُ بكِ', 'قطعٌ نُحتت من روحِ التاريخ، لتعيدَ إحياءَ هيبةِ الملوك.', 'images/phi.png'),
                                                                                            (4, 'victory', 'العصر الفيكتوري', 'أناقة ملكية تتجاوز الزمان', 'تصاميم تفيض بالأنوثة، مستوحاة من رقي العصر الفيكتوري.', 'images/vic.png'),
                                                                                            (5, 'othmani', 'الحضارة العثمانية', 'الحضارة العثمانية  تفوح بعبق الاصالة', 'هي الجمال حين يجمع عبق العرب ونقش الاتراك.', 'images/category_20260512_233159_d9e1e46f.png');

CREATE TABLE `contact_messages` (
                                    `id` int(11) NOT NULL,
                                    `name` varchar(100) NOT NULL,
                                    `email` varchar(150) NOT NULL,
                                    `message` text NOT NULL,
                                    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
                                                                                    (1, 'sondos', 'sondos.yaseen85@gmail.com', 'هناك خطأ في التسليم', '2026-05-11 20:10:26'),
                                                                                    (2, 'soos', 'sondos.yaseen@gmail.com', 'هناك منتج لم يصلني', '2026-05-11 20:11:36'),
                                                                                    (3, 'soos', 'sondos.yaseen@gmail.com', 'هناك منتج لم يصلني', '2026-05-11 20:37:24');

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

INSERT INTO `orders` (`id`, `user_id`, `fname`, `lname`, `email`, `phone`, `address`, `subtotal`, `shipping`, `discount`, `total`, `status`, `created_at`) VALUES
                                                                                                                                                               (24, NULL, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'kkl', 6110.00, 50.00, 305.50, 5854.50, 'confirmed', '2026-05-08 14:56:28'),
                                                                                                                                                               (26, 1, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'kilo', 4100.00, 50.00, 205.00, 3945.00, 'confirmed', '2026-05-08 15:39:30'),
                                                                                                                                                               (27, 1, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'allt', 16800.00, 50.00, 840.00, 16010.00, 'shipped', '2026-05-09 09:47:27'),
                                                                                                                                                               (30, 1, 'guyjk,', 'gk.jlvgkjh', 'sondos.yaseen@gmail.com', '0594916840', 'ljhblin', 5000.00, 50.00, 250.00, 4800.00, 'pending', '2026-05-09 10:10:56'),
                                                                                                                                                               (31, 1, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'allt', 16800.00, 50.00, 840.00, 16010.00, 'delivered', '2026-05-07 09:47:27'),
                                                                                                                                                               (32, 1, 'guyjk,', 'gk.jlvgkjh', 'sondos.yaseen@gmail.com', '0594916840', 'ljhblin', 5000.00, 50.00, 250.00, 4800.00, 'pending', '2026-05-06 10:10:56'),
                                                                                                                                                               (33, 1, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'kilo', 4100.00, 50.00, 205.00, 3945.00, 'confirmed', '2026-05-04 15:39:30'),
                                                                                                                                                               (34, NULL, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'kkl', 6110.00, 50.00, 305.50, 5854.50, 'pending', '2026-05-03 14:56:28'),
                                                                                                                                                               (35, 1, 'sondos', 'yaseen', 'sondos.yaseen@gmail.com', '0598352860', 'allt', 16800.00, 50.00, 840.00, 16010.00, 'shipped', '2026-05-02 09:47:27'),
                                                                                                                                                               (36, 4, 'sandi', 'yaseen', 'sabaayaseen52@gmail.com', '0594916840', 'nablus', 5450.00, 50.00, 272.50, 5227.50, 'delivered', '2026-05-12 20:06:40');

CREATE TABLE `order_items` (
                               `id` int(11) NOT NULL,
                               `order_id` int(11) NOT NULL,
                               `product_id` int(11) NOT NULL,
                               `product_name` varchar(200) NOT NULL,
                               `quantity` int(11) NOT NULL,
                               `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`) VALUES
                                                                                                    (1, 24, 1, 'طقم أواني خزف أندلسي', 1, 850.00),
                                                                                                    (2, 24, 2, 'وشاح حرير مزخرف', 2, 1200.00),
                                                                                                    (3, 24, 3, 'مصباح نحاسي تراثي', 2, 450.00),
                                                                                                    (4, 24, 5, 'سجادة صلاة تقليدية', 1, 980.00),
                                                                                                    (5, 24, 21, 'صندوق مجوهرات فاخر', 1, 980.00),
                                                                                                    (9, 26, 1, 'طقم أواني خزف أندلسي', 2, 850.00),
                                                                                                    (10, 26, 2, 'وشاح حرير مزخرف', 2, 1200.00),
                                                                                                    (11, 27, 7, 'ثوب ملكي من حيفا', 6, 2800.00),
                                                                                                    (18, 30, 6, 'ثوب فلسطيني تقليدي', 1, 2500.00),
                                                                                                    (19, 30, 8, 'برقع + عرجة', 1, 2500.00),
                                                                                                    (20, 36, 6, 'ثوب فلسطيني تقليدي', 1, 2500.00),
                                                                                                    (21, 36, 8, 'برقع + عرجة', 1, 2500.00),
                                                                                                    (22, 36, 3, 'مصباح نحاسي تراثي', 1, 450.00);

CREATE TABLE `products` (
                            `id` int(11) NOT NULL,
                            `name` varchar(200) NOT NULL,
                            `description` text DEFAULT NULL,
                            `price` decimal(10,2) NOT NULL,
                            `image` varchar(255) DEFAULT NULL,
                            `category` varchar(50) NOT NULL,
                            `stock` int(11) DEFAULT 0,
                            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                            `subcategory` enum('ديكور','اكسسوارات','ملابس') NOT NULL DEFAULT 'ديكور'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`, `subcategory`) VALUES
                                                                                                                             (1, 'طقم أواني خزف أندلسي', 'طقم فاخر مصنوع من السيراميك عالي الجودة، مزين بنقوش هندسية مستوحاة من قصر الحمراء، يضيف لمسة ملكية على مائدتك.', 850.00, 'images/item1.png', 'andalus', 17, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (2, 'وشاح حرير مزخرف', 'وشاح من الحرير الطبيعي الناعم، يتميز بألوان زاهية وزخارف نباتية دقيقة، مثالي لإطلالة راقية وعصرية بلمسة تراثية.', 1200.00, 'images/item2.png', 'andalus', 10, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (3, 'مصباح نحاسي تراثي', 'قطعة فنية مشغولة يدوياً من النحاس الخالص، تعكس ظلالاً ساحرة وتضفي أجواءً من الدفء والأصالة على زوايا منزلك.', 450.00, 'images/item3.png', 'andalus', 26, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (4, 'طقم أندلوسي فاخر جدًا', 'سخة حصرية وراقية بتفاصيل مذهبة دقيقة، مصمم خصيصاً للمناسبات الكبرى ولمحبي الاقتناء المتميز.', 3200.00, 'images/item4.png', 'andalus', 8, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (5, 'سجادة صلاة تقليدية', 'سجادة صلاة مريحة مبطنة، محاكة بخيوط متينة وتصميم كلاسيكي يجمع بين البساطة والروحانية.', 980.00, 'images/item5.png', 'andalus', 11, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (6, 'ثوب فلسطيني تقليدي', 'قطعة تراثية فريدة تجسد الهوية الفلسطينية، مطرزة يدوياً بدقة عالية وبألوان كلاسيكية تعكس عراقة الماضي وأصالة الحرفة.', 2500.00, 'images/item11.png', 'sham', 8, '2026-04-23 08:01:26', 'ملابس'),
                                                                                                                             (7, 'ثوب ملكي من حيفا', 'تصميم فاخر مستوحى من جمال مدينة حيفا، يتميز بقصات ملكية وتفاصيل تطريز غنية تجعل منه خياراً مثالياً للمناسبات الكبرى والباحثات عن التميز.', 2800.00, 'images/item12.png', 'sham', 0, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (8, 'برقع + عرجة', 'طقم إكسسوارات تراثي يجمع بين \"العرجة\" المزدانة بالقطع المعدنية والبرقع التقليدي، لإضافة لمسة أصيلة وجمالية على الزي الشعبي', 2500.00, 'images/item13.png', 'sham', 1, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (9, 'خاتم تطريز يدوي ذهبي', 'قطعة فنية صغيرة تحمل تفاصيل التطريز الدقيق داخل إطار ذهبي أنيق، لتمزج بين الحداثة في التصميم وجمال التراث اليدوي.', 1500.00, 'images/item14.png', 'sham', 18, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (10, 'بلاط السيراميك الشامي', 'قطعة ديكور فاخرة مستوحاة من البيوت الشامية القديمة، تتميز بزخارف هندسية ملونة تضفي لمسة فنية وروحاً دمشقية على أركان المنزل.', 750.00, 'images/item15.png', 'sham', 22, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (11, 'فستان الكرينولين الفكتوري', 'فستان فاخر يجسد أناقة العصر الفيكتوري بامتياز، يتميز بتصميم التنورة الواسعة المدعومة بإطار الكرينولين التقليدي، مع تفاصيل دقيقة من الدانتيل والحرير لإطلالة ملكية تاريخية.', 3600.00, 'images/item21.png', 'victory', 6, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (12, 'خزانة الكيدينزا الفكتورية', 'قطعة أثاث كلاسيكية مصنوعة من الخشب الصلب المنحوت يدوياً، تتميز بنقوش غنية وتفاصيل فاخرة، توفر مساحة تخزين أنيقة وتضفي فخامة تاريخية على أي غرفة.', 2800.00, 'images/item22.png', 'victory', 10, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (13, 'ياقة فكتورية عالية', 'إكسسوار راقٍ مستوحى من أزياء النبلاء، مصنوعة من الدانتيل الفاخر بتصميم يلتف حول الرقبة بنعومة، مثالية لإضافة لمسة \"أنتيك\" كلاسيكية على الفساتين أو القمصان الحديثة.', 800.00, 'images/item23.png', 'victory', 3, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (14, 'قبعة دانتيل فكتورية', 'قبعة ناعمة مشغولة بخيوط الدانتيل الرقيقة، مصممة بأسلوب أنثوي كلاسيكي يعود بنا إلى زمن الحفلات الأرستقراطية، وهي قطعة مكملة لا غنى عنها لإطلالة تراثية متكاملة.', 950.00, 'images/item24.png', 'victory', 16, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (15, 'مظلة دانتيل فكتورية', 'مظلة شمسية يدوية مزينة بزخارف الدانتيل والمقبض الخشبي المنحوت، تعكس رقيّ السيدات في القرن التاسع عشر وتعتبر قطعة مثالية للتصوير أو المناسبات الفاخرة.', 1100.00, 'images/item25.png', 'victory', 3, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (16, 'فستان الكالاسيريس الفرعوني', 'ثوب أيقوني مستوحى من أزياء ملكات النيل، يتميز بقصته الانسيابية الطويلة التي تمنح القوام رشاقة ومظهراً مهيباً، مصنوع من قماش خفيف يحاكي الكتان المصري الفاخر.', 1100.00, 'images/item31.png', 'egypt', 13, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (17, 'الصديرية الذهبية مع الخرز الأزرق', 'قطعة مجوهرات عريضة (Wesekh) تعكس بريق الشمس، مرصعة بخرز يحاكي لون \"اللازورد\" الملكي، صممت لتوضع فوق الأكتاف لتضيف لمسة من العظمة الفرعونية على أي زي.', 2200.00, 'images/item32.png', 'egypt', 8, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (18, 'فستان الأردية المكسرة', 'تصميم فريد يعتمد على أسلوب \"الطيّات\" الدقيقة التي اشتهر بها نبلاء مصر القديمة، يعطي إيحاءً بالتحرك والتموج مع كل خطوة، ويجمع بين الاحتشام والأناقة التاريخية.', 3800.00, 'images/item33.png', 'egypt', 11, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (19, 'قلادة التعويذة الفرعونية', 'قلادة بتصميم تعويذة عين حورس الأصيلة,تتميز بتصميم دائري متناغم يرمز للأبدية، وهي قطعة مثالية للارتداء اليومي مع الحفاظ على روح التراث.', 700.00, 'images/item34.png', 'egypt', 4, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (20, 'تاج أفعى فرعوني', 'تاج \"اليورايوس\" الشهير الذي يرمز للقوة والحماية، مصمم بدقة عالية ليحاكي تيجان الفراعنة، مرصع بأحجار ملونة تضفي طابعاً ملكياً قوياً لصاحبته.', 2900.00, 'images/item35.png', 'egypt', 5, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (21, 'صندوق مجوهرات فاخر', 'صندوق خشبي مطعم بالصدف أو النحاس، مبطن من الداخل بالمخمل لحفظ مقتنياتك الثمينة بأناقة وأمان.', 980.00, 'images/item6.png', 'andalus', 12, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (22, 'سجادة أندلوسية غرزة قرطبة', 'سجادة حائط أو أرضية تعتمد \"غرزة قرطبة\" الشهيرة، تحكي تفاصيلها قصة الفن العريق في الأندلس بألوان دافئة.', 1200.00, 'images/item7.png', 'andalus', 20, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (23, 'قفطان أندلوسي أخضر مذهب', 'رداء تقليدي من المخمل الأخضر الفاخر، مطرز بخيوط القصب الذهبية، يجسد الفخامة الأندلسية في أبهى صورها.', 3200.00, 'images/item8.png', 'andalus', 31, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (24, 'خاتم أندلوسي مرصع بالياقوت الأخضر', 'خاتم من الفضة الإسترلينية بتصميم \"أرابيسك\" يدوي، يتوسطه حجر ياقوت أخضر جذاب يخطف الأنظار.', 300.00, 'images/item9.png', 'andalus', 27, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (25, 'حزام مطرز بتصميم عصري', 'حزام أنيق يجمع بين القماش الفاخر وتطريز \"الغرزة\" التقليدية، مصمم ليتناسب مع الملابس الحديثة ويمنحها طابعاً شرقياً مميزاً.', 4500.00, 'images/item16.png', 'sham', 5, '2026-04-22 11:08:26', 'اكسسوارات'),
                                                                                                                             (26, 'مجموعة أواني نحاسية مخرمة', 'طقم من النحاس الخالص المشغول يدوياً بدقة، يتميز بنقوش مخرمة تسمح بمرور الضوء والظل، مما يجعله قطعة تقديم وديكور استثنائية.', 1500.00, 'images/item17.png', 'sham', 11, '2026-04-21 11:08:26', 'ديكور'),
                                                                                                                             (27, 'صندوق الموزاييك الدمشقي', 'صندوق خشبي مصنوع يدوياً ومطعم بقطع الصدف والخشب الملون (الموزاييك)، مثالي لحفظ المجوهرات والمقتنيات الثمينة بأسلوب فني راقٍ.', 2500.00, 'images/item18.png', 'sham', 14, '2026-04-21 11:08:26', 'ديكور'),
                                                                                                                             (28, 'خاتم شامي بتصميم عصري', 'خاتم مبتكر مستوحى من النقوش الدمشقية العريقة، يقدم التراث السوري بقالب عصري بسيط يناسب الإطلالات اليومية.', 500.00, 'images/item19.png', 'sham', 5, '2026-04-21 11:08:26', 'اكسسوارات'),
                                                                                                                             (29, 'أريكة الشيزلونج الفكتورية', 'قطعة استرخاء استثنائية منجدة بالمخمل الفاخر، تتميز بانحناءات مريحة وأرجل خشبية مزخرفة، تجمع بين الراحة والجمال الفني لتكون قطعة مركزية في ديكور منزلك.', 4000.00, 'images/item26.png', 'victory', 5, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (30, 'طاولة فيكتورية بأرجل مقوسة', 'طاولة جانبية أنيقة تتميز بأرجل \"كابريول\" المقوسة الشهيرة في العمارة الفيكتورية، خفيفة الوزن في تصميمها لكنها قوية في حضورها الجمالي بين قطع الأثاث.', 3500.00, 'images/item27.png', 'victory', 7, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (31, 'بروش الكاميو', 'دبوس صدر (بروش) كلاسيكي يحمل نقشاً بارزاً لوجه سيدة داخل إطار بيضاوي مذهب، قطعة مجوهرات أيقونية تعبر عن الذوق الرفيع والارتباط بالفن الكلاسيكي القديم.', 500.00, 'images/item28.png', 'victory', 14, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (32, 'الأكمام المنفوخة + مشد الخصر', 'طقم يجمع بين \"الكورسيه\" الذي يحدد الخصر بجمالية فائقة، والأكمام المنفوخة (Puff Sleeves) التي كانت رمزاً للأناقة في العصر الفيكتوري، لإطلالة درامية وجذابة.', 2000.00, 'images/item29.png', 'victory', 18, '2026-05-07 11:08:26', 'ملابس'),
                                                                                                                             (33, 'كراسي العرش', 'كرسي فني مستوحى من كنوز الملك توت عنخ آمون، يتميز بأرجل على شكل مخالب أسد ونقوش محفورة بعناية، مطلي بلمسات ذهبية تجعل منه قطعة أثاث مركزية تخطف الأنظار.', 3400.00, 'images/item36.png', 'egypt', 15, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (34, 'المناضد اليومية الفرعونية', 'طاولة جانبية خشبية تجمع بين البساطة الهندسية والزخارف الفرعونية الهادئة، مصممة لتناسب الاستخدام اليومي في المنزل الحديث مع إضافة عبق التاريخ للغرفة.', 3900.00, 'images/item37.png', 'egypt', 12, '2026-05-07 11:08:26', 'ديكور'),
                                                                                                                             (35, 'أساور فرعونية عريضة', 'طقم أساور للمعصم بتصميم \"الكلبش\" العريض، مزينة بنقوش بارزة لرموز قديمة كزهرة اللوتس والجعران، تمنح اليد مظهراً قوياً وجذاباً.', 900.00, 'images/item38.png', 'egypt', 4, '2026-05-07 11:08:26', 'اكسسوارات'),
                                                                                                                             (36, 'سرير عريض فرعوني مميز', 'قطعة أثاث فاخرة تتميز برأسية سرير مرتفعة محفورة برموز الحماية والراحة، مصمم بأرجل مرتفعة وتفاصيل مذهبة ليحول غرفة النوم إلى جناح ملكي من العصور القديمة.', 2400.00, 'images/item39.png', 'egypt', 7, '2026-05-07 11:08:26', 'ديكور');

CREATE TABLE `settings` (
                            `setting_key` varchar(100) NOT NULL,
                            `setting_value` text NOT NULL DEFAULT '',
                            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
                                                                          ('admin_password', 'sonly', '2026-05-13 07:48:47'),
                                                                          ('footer_text', 'استكشف جمال الحضارات من خلال قطعنا الفريدة والمختارة بعناية.', '2026-05-11 19:33:38'),
                                                                          ('store_address', 'Palestine Nablus', '2026-05-11 18:27:04'),
                                                                          ('store_email', 'albousala@gmail.com', '2026-05-11 18:27:04'),
                                                                          ('store_name', 'البوصلة', '2026-05-11 15:36:20'),
                                                                          ('store_phone', '0598898003', '2026-05-11 15:36:20'),
                                                                          ('store_whatsapp', '+970598352860', '2026-05-11 18:27:04');

CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `name` varchar(150) NOT NULL,
                         `email` varchar(200) NOT NULL,
                         `password` varchar(250) NOT NULL,
                         `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
                                                                          (1, 'sondos', 'sondos.yaseen@gmail.com', '$2y$12$flnU8wOvlFAuBpXQUcOZo.Flx38kzrDHuT7Ek/GiLdc5vvyHjIJDG', '2026-05-07 09:06:10'),
                                                                          (4, 'sabaa', 'sabaayaseen52@gmail.com', '$2y$12$VT4ancvx.mOn6rCTFrv9JuD4ZAaHqGneyaz3L31epfQu/.IP5td8i', '2026-05-12 20:05:54');

ALTER TABLE `accounts`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `cart_items`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `categories`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

ALTER TABLE `contact_messages`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `orders`
    ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `order_items`
    ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

ALTER TABLE `products`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
    ADD PRIMARY KEY (`setting_key`);

ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `accounts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `cart_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

ALTER TABLE `categories`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `contact_messages`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `orders`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `order_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `products`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `cart_items`
    ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `orders`
    ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items`
    ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

;
;
;
