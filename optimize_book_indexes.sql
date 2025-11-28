-- Performance optimization indexes for book.php
-- Add these indexes to speed up book listing queries

-- Index for book_category_map table
CREATE INDEX IF NOT EXISTS idx_book_category_map_book_id ON book_category_map(book_id);
CREATE INDEX IF NOT EXISTS idx_book_category_map_category_id ON book_category_map(category_id);

-- Index for book_subject_map table  
CREATE INDEX IF NOT EXISTS idx_book_subject_map_book_id ON book_subject_map(book_id);
CREATE INDEX IF NOT EXISTS idx_book_subject_map_subject_id ON book_subject_map(subject_id);

-- Index for borrow_transactions table
CREATE INDEX IF NOT EXISTS idx_borrow_transactions_book_id ON borrow_transactions(book_id);
CREATE INDEX IF NOT EXISTS idx_borrow_transactions_status ON borrow_transactions(status);
CREATE INDEX IF NOT EXISTS idx_borrow_transactions_book_status ON borrow_transactions(book_id, status);

-- Index for books table
CREATE INDEX IF NOT EXISTS idx_books_id_date ON books(id, date_created);
