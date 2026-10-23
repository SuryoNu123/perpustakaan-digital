<?php
session_start();


define('BOOKS_FILE', __DIR__ . '/../data/books.json');
define('GENRES_FILE', __DIR__ . '/../data/genres.json');
define('BOOK_COUNTS_FILE', __DIR__ . '/../data/book_counts.json');


function login($username, $password) {
 
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    return true;
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}


function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function loadJSON($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

function saveJSON($file, $data) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


function getAllBooks() {
    return loadJSON(BOOKS_FILE);
}

function getBookById($id) {
    $books = getAllBooks();
    foreach ($books as $book) {
        if ($book['id'] == $id) {
            return $book;
        }
    }
    return null;
}

function addBook($title, $author, $isbn, $genre, $subgenre, $year, $pages, $description) {
    $books = getAllBooks();
    
 
    foreach ($books as $book) {
        if ($book['isbn'] === $isbn) {
            return false;
        }
    }
    
    $newBook = [
        'id' => count($books) + 1,
        'title' => $title,
        'author' => $author,
        'isbn' => $isbn,
        'genre' => $genre,
        'subgenre' => $subgenre,
        'year' => $year,
        'pages' => $pages,
        'description' => $description,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $books[] = $newBook;
    return saveJSON(BOOKS_FILE, $books);
}

function updateBook($id, $title, $author, $isbn, $genre, $subgenre, $year, $pages, $description) {
    $books = getAllBooks();
    
    foreach ($books as &$book) {
        if ($book['id'] == $id) {
            $book['title'] = $title;
            $book['author'] = $author;
            $book['isbn'] = $isbn;
            $book['genre'] = $genre;
            $book['subgenre'] = $subgenre;
            $book['year'] = $year;
            $book['pages'] = $pages;
            $book['description'] = $description;
            $book['updated_at'] = date('Y-m-d H:i:s');
            return saveJSON(BOOKS_FILE, $books);
        }
    }
    return false;
}

function deleteBook($id) {
    $books = getAllBooks();
    $books = array_filter($books, function($book) use ($id) {
        return $book['id'] != $id;
    });
    return saveJSON(BOOKS_FILE, array_values($books));
}

function getRecentBooks($limit = 5) {
    $books = getAllBooks();
    usort($books, function($a, $b) {
        $timeA = strtotime($a['created_at'] ?? '1970-01-01');
        $timeB = strtotime($b['created_at'] ?? '1970-01-01');
        return $timeB - $timeA;
    });
    return array_slice($books, 0, $limit);
}


function getGenres() {
    return loadJSON(GENRES_FILE);
}

function addGenre($genre) {
    $genres = getGenres();
    if (!isset($genres[$genre])) {
        $genres[$genre] = [];
        $result = saveJSON(GENRES_FILE, $genres);
        if ($result) {
            initializeGenreBookCount($genre);
        }
        return $result;
    }
    return false;
}

function addSubgenre($genre, $subgenre) {
    $genres = getGenres();
    if (isset($genres[$genre]) && !in_array($subgenre, $genres[$genre])) {
        $genres[$genre][] = $subgenre;
        $result = saveJSON(GENRES_FILE, $genres);
        if ($result) {
            initializeSubgenreBookCount($genre, $subgenre);
        }
        return $result;
    }
    return false;
}

function deleteGenre($genre) {
    $genres = getGenres();
    if (isset($genres[$genre])) {
        unset($genres[$genre]);
        $result = saveJSON(GENRES_FILE, $genres);
        if ($result) {
            cleanupBookCounts($genre);
        }
        return $result;
    }
    return false;
}

function deleteSubgenre($genre, $subgenre) {
    $genres = getGenres();
    if (isset($genres[$genre])) {
        $genres[$genre] = array_filter($genres[$genre], function($sg) use ($subgenre) {
            return $sg !== $subgenre;
        });
        $genres[$genre] = array_values($genres[$genre]);
        $result = saveJSON(GENRES_FILE, $genres);
        if ($result) {
            cleanupBookCounts($genre, $subgenre);
        }
        return $result;
    }
    return false;
}

function getGenreStats() {
    $books = getAllBooks();
    $stats = [];
    
    foreach ($books as $book) {
        $genre = $book['genre'];
        if (!isset($stats[$genre])) {
            $stats[$genre] = 0;
        }
        $stats[$genre]++;
    }
    
    return $stats;
}


function getStats() {
    $books = getAllBooks();
    $genres = getGenres();
    
    return [
        'total_books' => count($books),
        'total_genres' => count($genres),
        'active_users' => 156, 
        'books_borrowed' => 89  
    ];
}


function updateBookCount($genre, $count, $subgenre = '') {
    $bookCounts = loadJSON(BOOK_COUNTS_FILE);
    
    if (!isset($bookCounts[$genre])) {
        $bookCounts[$genre] = [
            'genre_count' => 0,
            'subgenres' => []
        ];
    }
    
    if (empty($subgenre)) {
       
        $bookCounts[$genre]['genre_count'] = (int)$count;
    } else {
       
        $bookCounts[$genre]['subgenres'][$subgenre] = (int)$count;
    }
    
    return saveJSON(BOOK_COUNTS_FILE, $bookCounts);
}


function getBookCountForCategory($genre, $subgenre = '') {
    $bookCounts = loadJSON(BOOK_COUNTS_FILE);
    
    if (!isset($bookCounts[$genre])) {
        return 0;
    }
    
    if (empty($subgenre)) {
        return $bookCounts[$genre]['genre_count'] ?? 0;
    } else {
        return $bookCounts[$genre]['subgenres'][$subgenre] ?? 0;
    }
}


function getAllBookCounts() {
    return loadJSON(BOOK_COUNTS_FILE);
}


function initializeGenreBookCount($genre) {
    $bookCounts = loadJSON(BOOK_COUNTS_FILE);
    
    if (!isset($bookCounts[$genre])) {
        $bookCounts[$genre] = [
            'genre_count' => 0,
            'subgenres' => []
        ];
        return saveJSON(BOOK_COUNTS_FILE, $bookCounts);
    }
    
    return true;
}


function initializeSubgenreBookCount($genre, $subgenre) {
    $bookCounts = loadJSON(BOOK_COUNTS_FILE);
    
    if (!isset($bookCounts[$genre])) {
        $bookCounts[$genre] = [
            'genre_count' => 0,
            'subgenres' => []
        ];
    }
    
    if (!isset($bookCounts[$genre]['subgenres'][$subgenre])) {
        $bookCounts[$genre]['subgenres'][$subgenre] = 0;
        return saveJSON(BOOK_COUNTS_FILE, $bookCounts);
    }
    
    return true;
}


function cleanupBookCounts($genre, $subgenre = '') {
    $bookCounts = loadJSON(BOOK_COUNTS_FILE);
    
    if (empty($subgenre)) {
        
        if (isset($bookCounts[$genre])) {
            unset($bookCounts[$genre]);
            return saveJSON(BOOK_COUNTS_FILE, $bookCounts);
        }
    } else {
  
        if (isset($bookCounts[$genre]['subgenres'][$subgenre])) {
            unset($bookCounts[$genre]['subgenres'][$subgenre]);
            return saveJSON(BOOK_COUNTS_FILE, $bookCounts);
        }
    }
    
    return true;
}
?>
