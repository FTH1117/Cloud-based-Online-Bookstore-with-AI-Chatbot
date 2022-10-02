-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2022 at 05:10 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoppingcart_advanced`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(100) NOT NULL,
  `address_zip` varchar(50) NOT NULL,
  `address_country` varchar(100) NOT NULL,
  `registered` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `email`, `password`, `role`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `registered`) VALUES
(1, 'admin@website.com', '$2y$10$pEHRAE4Ia0mE9BdLmbS.ueQsv/.WlTUSW7/cqF/T36iW.zDzSkx4y', 'Admin', 'John', 'Doe', '98 High Street', 'New York', 'NY', '10001', 'United States', '2022-01-01 00:00:00'),
(2, 'fzh200017@gmail.com', '$2y$10$kldRtwUZzhAR6PAfGrtRqufK8Om/eaXvPSlG77CXNozd3GDOGqYtK', 'Member', 'Foong', 'Tze Hing', 'asd', 'asd', 'asd', '123', 'United States', '2022-09-25 22:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'Sale', 0),
(3, 'Western Fiction', 0),
(4, 'Historical Fiction', 0),
(5, 'World History', 0),
(6, 'History', 0),
(7, 'Children\'s book', 0),
(8, 'Literary Fiction', 0),
(9, 'Teen & Young Adult Fiction', 0),
(10, 'Crime Mysteries', 0);

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `category_ids` varchar(50) NOT NULL,
  `product_ids` varchar(50) NOT NULL,
  `discount_code` varchar(50) NOT NULL,
  `discount_type` enum('Percentage','Fixed') NOT NULL,
  `discount_value` decimal(7,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `category_ids`, `product_ids`, `discount_code`, `discount_type`, `discount_value`, `start_date`, `end_date`) VALUES
(1, '', '', 'newyear2022', 'Percentage', '5.00', '2022-01-01 00:00:00', '2022-12-31 00:00:00'),
(2, '', '', '5off', 'Fixed', '5.00', '2022-01-01 00:00:00', '2032-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `date_uploaded` datetime NOT NULL DEFAULT current_timestamp(),
  `full_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `title`, `caption`, `date_uploaded`, `full_path`) VALUES
(1, 'Watch Front', '', '2022-02-14 15:58:10', 'uploads/watch.jpg'),
(2, 'Watch Side', '', '2022-02-14 15:58:10', 'uploads/watch-2.jpg'),
(3, 'Watch Back', '', '2022-02-14 15:58:10', 'uploads/watch-3.jpg'),
(4, 'Wallet', '', '2022-02-15 02:06:00', 'uploads/wallet.jpg'),
(5, 'Camera', '', '2022-03-04 16:03:37', 'uploads/camera.jpg'),
(6, 'Headphones', '', '2022-03-04 16:03:37', 'uploads/headphones.jpg'),
(7, '1-borden chantry.jpg', '', '2022-09-29 15:46:16', 'uploads/1-borden chantry.jpg'),
(8, 'a-world-undone.jpg', '', '2022-09-29 15:50:09', 'uploads/a-world-undone.jpg'),
(9, 'holler-of-the-fireflies.jpg', '', '2022-09-29 15:54:50', 'uploads/holler-of-the-fireflies.jpg'),
(10, 'the-morning-star.jpg', '', '2022-09-29 16:05:43', 'uploads/the-morning-star.jpg'),
(11, 'tanner-law.jpg', '', '2022-09-29 16:10:29', 'uploads/tanner-law.jpg'),
(12, 'luck-of-the-titanic.jpg', '', '2022-09-29 16:17:22', 'uploads/luck-of-the-titanic.jpg'),
(13, 'the-shadow-murders.jpg', '', '2022-09-29 16:19:49', 'uploads/the-shadow-murders.jpg'),
(14, 'magic-tree-house.jpg', '', '2022-09-29 16:22:36', 'uploads/magic-tree-house.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `author` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `isbn` bigint(200) NOT NULL,
  `rrp` decimal(7,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `weight` decimal(7,2) NOT NULL DEFAULT 0.00,
  `url_slug` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `author`, `type`, `isbn`, `rrp`, `quantity`, `date_added`, `weight`, `url_slug`, `status`) VALUES
(1, 'Watch', '<p>Unique watch made with stainless steel, ideal for those that prefer interative watches.</p>\r\n<h3>Features</h3>\r\n<ul>\r\n<li>Powered by Android with built-in apps.</li>\r\n<li>Adjustable to fit most.</li>\r\n<li>Long battery life, continuous wear for up to 2 days.</li>\r\n<li>Lightweight design, comfort on your wrist.</li>\r\n</ul>', '29.99', 'Testing', 1, 1234567890123, '0.00', 500, '2022-01-01 00:00:00', '1.00', 'smart-watch', 1),
(2, 'Tanner\'s Law', '<h3>A soldier gets swept up in the gold rush in this western from Charles G. West….</h3>\r\n\r\n<p>Tanner Bland leaves his Virginia home on foot to fight for Dixie and rides back on a fine horse. Most folks would call that a profit—until they consider his loss. With everyone thinking the soldier dead, his younger brother marries Tanner’s fiancée hours before he returns.\r\n \r\nHis home no longer his own, Tanner heads west to join an old army pal and hit the gold mines of Montana. But the wagon train the men join is not what they are hoping for. In the train with them are the four Leach brothers, each one meaner than the next. Now, if Tanner and his buddy want to make it to Montana alive, they’ll have to keep their enemies close…and their weapons closer.</p>', '37.00', 'Charles G. West', 1, 9780593441473, '0.00', 50, '2022-09-29 00:00:00', '1.00', 'tanners-law', 1),
(3, 'Luck of the Titanic', '<h3>From the New York Times bestselling author of The Downstairs Girl comes the richly imagined story of Valora and Jamie Luck, twin British-Chinese acrobats traveling aboard the Titanic on its ill-fated maiden voyage.</h3>\r\n\r\n<p>Valora Luck has two things: a ticket for the biggest and most luxurious ocean liner in the world, and a dream of leaving England behind and making a life for herself as a circus performer in New York. Much to her surprise though, she’s turned away at the gangway; apparently, Chinese aren’t allowed into America.\r\n\r\nBut Val has to get on that ship. Her twin brother Jamie, who has spent two long years at sea, is there, as is an influential circus owner, whom Val hopes to audition for. Thankfully, there’s not much a trained acrobat like Val can’t overcome when she puts her mind to it.\r\n\r\nAs a stowaway, Val should keep her head down and stay out of sight. But the clock is ticking and she has just seven days as the ship makes its way across the Atlantic to find Jamie, perform for the circus owner, and convince him to help get them both into America.\r\n\r\nThen one night the unthinkable happens, and suddenly Val’s dreams of a new life are crushed under the weight of the only thing that matters: survival.</p>', '55.63', 'Stacey Lee', 1, 9781524741006, '0.00', 50, '2022-09-29 00:00:00', '2.00', 'luck-of-the-titanic', 1),
(4, 'The Shadow Murders', '<h3>In the exhilarating penultimate thriller of the New York Times and #1 internationally bestselling Department Q series, the team must hunt for a nefarious criminal who has slipped under the radar for decades.</h3>\r\n\r\n<p>On her sixtieth birthday, a woman takes her own life. When the case lands on Detective Carl Mørck’s desk, he can’t imagine what this has to do with Department Q, Copenhagen’s cold cases division since the cause of death seems apparent. However, his superior, Marcus Jacobsen, is convinced that this is related to an unsolved case that has been plaguing him since 1988.\r\n \r\nAt Marcus’s behest, Carl and the Department Q gang—Rose, Assad, and Gordon—reluctantly begin to investigate. And they quickly discover that Marcus is onto something: Every two years for the past three decades, there have been unusual, impeccably timed deaths with connections between them that cannot be ignored, including mysterious piles of salt at the scenes. As the investigation goes deeper, it emerges that these “accidents” are in fact part of a sinister murder scheme.\r\n \r\nFaced with their toughest case yet, made only more difficult by COVID-19 restrictions and the challenges of their personal lives, the Department Q team must race to find the culprit before the next murder is committed, as it is becoming increasingly clear that the killer is far from finished.</p>', '130.00', 'Jussi Adler-Olsen', 1, 9781524742584, '0.00', 50, '2022-09-29 00:00:00', '1.00', 'the-shadow-murders', 1),
(8, 'Pirates Past Noon Graphic Novel', '<p>Captured by pirates! When Jack and Annie are whisked away in the magic tree house, they arrive on a beautiful beach. It’s paradise! That is, until the pirates arrive. . .\r\n \r\nThe dreaded Cap’n Bones is looking for buried treasure. He thinks Jack and Annie know where it is. And he’s not letting them out of his sight until they find it!</p>', '18.80', 'Mary Pope Osborne', 1, 9780593174807, '0.00', 50, '2022-09-27 05:42:00', '1.00', 'pirates-past-noon-graphic-novel', 1),
(9, 'Borden Chantry (Louis L\'Amour\'s Lost Treasures)', '<h3>As part of the Louis L’Amour’s Lost Treasures series, this edition contains exclusive bonus materials!</h3>\r\n\r\n<p>The marshal’s name was Borden Chantry. Young, lean, rugged, he’s buried a few men in this two-bit cow town—every single one killed in a fair fight. Then, one dark, grim day a mysterious gunman shot a man in cold blood. Five grisly murders later, Chantey was faced with the roughest assignment of his life—find that savage, trigger-happy hard case before he blasts apart every man in town . . . one by bloody one.</p>', '37.00', 'Louis L’Amour', 1, 9780593159804, '0.00', 50, '2022-09-29 15:42:00', '2.00', 'borden-chantry', 1),
(10, 'A World Undone', '<h3>“Thundering, magnificent . . . [A World Undone] is a book of true greatness that prompts moments of sheer joy and pleasure. . . . It will earn generations of admirers.”—The Washington Times</h3>\r\n\r\n<p>On a summer day in 1914, a nineteen-year-old Serbian nationalist gunned down Archduke Franz Ferdinand in Sarajevo. While the world slumbered, monumental forces were shaken. In less than a month, a combination of ambition, deceit, fear, jealousy, missed opportunities, and miscalculation sent Austro-Hungarian troops marching into Serbia, German troops streaming toward Paris, and a vast Russian army into war, with England as its ally. As crowds cheered their armies on, no one could guess what lay ahead in the First World War: four long years of slaughter, physical and moral exhaustion, and the near collapse of a civilization that until 1914 had dominated the globe.</p>', '101.98', 'G. J. Meyer', 1, 9780553382402, '0.00', 50, '2022-09-29 15:48:00', '2.00', 'world-undone', 1),
(11, 'Holler of the Fireflies', '<h3>A boy from the hood in Brooklyn travels to a STEM camp in an Appalachian holler for one epic, life-changing summer.</h3>\r\n \r\n<h3>A brilliant new novel from the award-winning author of The Stars Beneath Our Feet.</h3>\r\n\r\n<p>Javari knew that West Virginia would be different from his home in Bushwick, Brooklyn. But his first day at STEM Camp in a little Appalachian town is still a shock. Though run-ins with the police are just the same here. Not good.</p>\r\n \r\n<p>Javari will learn a lot about science, tech, engineering, and math at camp. And also about rich people, racism, and hidden agendas. But it’s Cricket, a local boy, budding activist, and occasional thief, who will show him a different side of the holler—and blow his mind wide open.</p>\r\n \r\n<p>Javari is about to have that summer. Where everything gets messy and complicated and confusing . . . and you wouldn’t want it any other way.</p>', '50.00', 'David Barclay Moore', 0, 9781524701307, '0.00', 49, '2022-09-29 15:51:00', '0.00', 'holler-of-the-fireflies', 1),
(12, 'The Morning Star', '<h3>“Knausgaard is among the finest writers alive.” —Dwight Garner, New York Times</h3>\r\n\r\n<p>The international bestseller from the author of the renowned My Struggle series, The Morning Star is an astonishing, ambitious, and rich novel about what we don’t understand, and our attempts to make sense of our world nonetheless</p>\r\n\r\n<p>One long night in August, Arne and Tove are staying with their children in their summer house in southern Norway. Their friend Egil has his own place nearby. Kathrine, a priest, is flying home from a Bible seminar, questioning her marriage. Journalist Jostein is out drinking for the night, while his wife, Turid, a nurse at a psychiatric care unit, is on a night shift when one of her patients escapes. </p>\r\n \r\n<p>Above them all, a huge star suddenly appears blazing in the sky. It brings with it a mysterious sense of foreboding.</p>\r\n \r\n<p>Strange things start to happen as nine lives come together under the star. Hundreds of crabs amass on the road as Arne drives at night; Jostein receives a call about a death metal band found brutally murdered in a Satanic ritual; Kathrine conducts a funeral service for a man she met at the airport – but is he actually dead? </p>\r\n \r\n<p>The Morning Star is about life in all its mundanity and drama, the strangeness that permeates our world, and the darkness in us all. Karl Ove Knausgaard’s astonishing new novel, his first after the My Struggle cycle, goes to the utmost limits of freedom and chaos, to what happens when forces beyond our comprehension are unleashed and the realms of the living and the dead collide.</p>\r\n\r\n', '45.00', 'Karl Ove Knausgaard', 0, 9780399563430, '60.00', 50, '2022-09-29 16:03:00', '0.00', 'the-morning-star', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products_categories`
--

CREATE TABLE `products_categories` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_categories`
--

INSERT INTO `products_categories` (`id`, `product_id`, `category_id`) VALUES
(27, 2, 3),
(28, 2, 4),
(31, 3, 9),
(33, 4, 10),
(35, 8, 7),
(16, 9, 3),
(17, 9, 4),
(20, 9, 6),
(21, 10, 6),
(23, 11, 7),
(26, 12, 8);

-- --------------------------------------------------------

--
-- Table structure for table `products_downloads`
--

CREATE TABLE `products_downloads` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_downloads`
--

INSERT INTO `products_downloads` (`id`, `product_id`, `file_path`, `position`) VALUES
(1, 11, 'ebooks/holler-of-the-fireflies.pdf', 1),
(2, 12, 'ebooks/the-morning-star.pdf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products_media`
--

CREATE TABLE `products_media` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_media`
--

INSERT INTO `products_media` (`id`, `product_id`, `media_id`, `position`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 2),
(3, 1, 3, 3),
(8, 9, 7, 1),
(9, 10, 8, 1),
(10, 11, 9, 1),
(11, 12, 10, 1),
(12, 2, 11, 1),
(13, 3, 12, 1),
(14, 4, 13, 1),
(15, 8, 14, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products_options`
--

CREATE TABLE `products_options` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `price_modifier` enum('add','subtract') NOT NULL,
  `weight` decimal(7,2) NOT NULL,
  `weight_modifier` enum('add','subtract') NOT NULL,
  `type` enum('select','radio','checkbox','text','datetime') NOT NULL,
  `required` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products_options`
--

INSERT INTO `products_options` (`id`, `title`, `name`, `quantity`, `price`, `price_modifier`, `weight`, `weight_modifier`, `type`, `required`, `position`, `product_id`) VALUES
(1, 'Size', 'Small', -1, '9.99', 'add', '9.99', 'add', 'select', 1, 1, 1),
(2, 'Size', 'Large', -1, '8.99', 'add', '8.99', 'add', 'select', 1, 1, 1),
(3, 'Type', 'Standard', -1, '0.00', 'add', '0.00', 'add', 'radio', 1, 2, 1),
(4, 'Type', 'Deluxe', -1, '10.00', 'add', '0.00', 'add', 'radio', 1, 2, 1),
(5, 'Color', 'Red', -1, '1.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(6, 'Color', 'Yellow', -1, '2.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(7, 'Color', 'Blue', -1, '3.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(8, 'Color', 'Purple', -1, '4.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(9, 'Color', 'Brown', -1, '5.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(10, 'Color', 'Pink', -1, '6.00', 'add', '10.00', 'add', 'checkbox', 0, 3, 1),
(11, 'Color', 'Orange', -1, '8.00', 'add', '11.00', 'add', 'checkbox', 0, 3, 1),
(12, 'Delivery Date', '', -1, '5.00', 'add', '0.00', 'add', 'datetime', 0, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Single Product','Entire Order') NOT NULL DEFAULT 'Single Product',
  `countries` varchar(255) NOT NULL DEFAULT '',
  `price_from` decimal(7,2) NOT NULL,
  `price_to` decimal(7,2) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `weight_from` decimal(7,2) NOT NULL DEFAULT 0.00,
  `weight_to` decimal(7,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `name`, `type`, `countries`, `price_from`, `price_to`, `price`, `weight_from`, `weight_to`) VALUES
(1, 'J&T', 'Entire Order', '', '0.00', '99999.00', '3.99', '1.00', '99999.00'),
(2, 'Lalamove', 'Entire Order', '', '0.00', '99999.00', '7.99', '1.00', '99999.00'),
(3, 'Skynet', 'Entire Order', '', '0.00', '99999.00', '3.99', '1.00', '9999.00'),
(4, 'Pos Laju', 'Entire Order', '', '0.00', '9999.00', '3.99', '1.00', '9999.00'),
(5, 'None', 'Entire Order', '', '0.00', '99999.00', '0.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `rate` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `country`, `rate`) VALUES
(1, 'United Kingdom', '20.00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(30) NOT NULL,
  `created` datetime NOT NULL,
  `payer_email` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `address_street` varchar(255) NOT NULL DEFAULT '',
  `address_city` varchar(100) NOT NULL DEFAULT '',
  `address_state` varchar(100) NOT NULL DEFAULT '',
  `address_zip` varchar(50) NOT NULL DEFAULT '',
  `address_country` varchar(100) NOT NULL DEFAULT '',
  `account_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'website',
  `shipping_method` varchar(255) NOT NULL DEFAULT '',
  `shipping_amount` decimal(7,2) NOT NULL DEFAULT 0.00,
  `discount_code` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `txn_id`, `payment_amount`, `payment_status`, `created`, `payer_email`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `account_id`, `payment_method`, `shipping_method`, `shipping_amount`, `discount_code`) VALUES
(1, 'SC633069DD5C473853A3', '18.98', 'Completed', '2022-09-25 16:46:53', 'fzh200017@gmail.com', 'Foong', 'Tze Hing', 'asd', 'asd', 'asd', '123', 'United States', 2, 'website', 'Standard', '3.99', ''),
(2, 'SC6331DB4A29AB5B5E5E', '18.98', 'Completed', '2022-09-26 19:03:06', 'admin@website.com', 'John', 'Doe', '98 High Street', 'New York', 'NY', '10001', 'United States', 1, 'website', 'Standard', '3.99', ''),
(3, 'SC6332FD509E4815EDAB', '49.97', 'Completed', '2022-09-27 15:40:32', 'admin@website.com', 'John', 'Doe', '98 High Street', 'New York', 'NY', '10001', 'United States', 1, 'website', 'Standard', '3.99', ''),
(4, 'SC6333374F17D48D85AB', '123.00', 'Completed', '2022-09-27 19:47:59', 'admin@website.com', 'John', 'Doe', '98 High Street', 'New York', 'NY', '10001', 'United States', 1, 'website', '', '0.00', ''),
(5, 'SC6335A4A4C360DC21D7', '50.00', 'Completed', '2022-09-29 15:59:00', 'admin@website.com', 'John', 'Doe', '98 High Street', 'New York', 'NY', '10001', 'United States', 1, 'website', 'None', '0.00', '');

-- --------------------------------------------------------

--
-- Table structure for table `transactions_items`
--

CREATE TABLE `transactions_items` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_options` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transactions_items`
--

INSERT INTO `transactions_items` (`id`, `txn_id`, `item_id`, `item_price`, `item_quantity`, `item_options`) VALUES
(1, 'SC633069DD5C473853A3', 2, '14.99', 1, ''),
(2, 'SC6331DB4A29AB5B5E5E', 2, '14.99', 1, ''),
(3, 'SC6332FD509E4815EDAB', 1, '45.98', 1, 'Size-Small,Type-Standard,Color-Red,Delivery Date-2022-09-27T21:40'),
(4, 'SC6333374F17D48D85AB', 8, '123.00', 1, ''),
(5, 'SC6335A4A4C360DC21D7', 11, '50.00', 1, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products_categories`
--
ALTER TABLE `products_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`category_id`);

--
-- Indexes for table `products_downloads`
--
ALTER TABLE `products_downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`file_path`);

--
-- Indexes for table `products_media`
--
ALTER TABLE `products_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products_options`
--
ALTER TABLE `products_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`title`,`name`) USING BTREE;

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `transactions_items`
--
ALTER TABLE `transactions_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products_categories`
--
ALTER TABLE `products_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `products_downloads`
--
ALTER TABLE `products_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products_media`
--
ALTER TABLE `products_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products_options`
--
ALTER TABLE `products_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions_items`
--
ALTER TABLE `transactions_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
