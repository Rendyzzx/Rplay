-- Create the 'users' table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone_number VARCHAR(20) DEFAULT '628xxx',
  password VARCHAR(255) NOT NULL,
  verification_code VARCHAR(255) NOT NULL,
  is_verified INT DEFAULT 0,
  profile_picture VARCHAR(255),
  bio TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(username)
);

-- Create the 'videos' table
CREATE TABLE videos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  video VARCHAR(255),
  views INT DEFAULT 0,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create the 'subscribers' table
CREATE TABLE subscribers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  subscribed_user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  INDEX(subscribed_user_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (subscribed_user_id) REFERENCES users(id)
);

-- Create the 'likes' table
CREATE TABLE likes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  video_id INT,
  action ENUM('like', 'dislike'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  INDEX(video_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (video_id) REFERENCES videos(id)
);

-- Create the 'comments' table
CREATE TABLE comments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  video_id INT,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  INDEX(video_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (video_id) REFERENCES videos(id)
);