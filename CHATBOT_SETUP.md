# Chatbot & Profanity Filter Setup

## Features Implemented
✅ Dynamic Bible verse fetching using Bible API (FREE)
✅ Profanity filter for chat messages (English & Filipino)
✅ Connect to admin when user needs help
✅ Database-driven profanity word list

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Profanity Words
```bash
php artisan db:seed --class=ProfanityWordsSeeder
```

### 3. (Optional) Import Additional Profanity Words
Download a profanity list from:
- https://github.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words
- https://github.com/zacanger/profane-words

Then import:
```bash
php artisan profanity:import path/to/words.txt
```

### 4. Clear Cache
```bash
php artisan cache:clear
```

## Usage

### Chatbot Features
- **Bible Verses**: Ask "Show me John 3:16" or "Bible verse about love"
- **Prayer Requests**: Say "I need prayer"
- **Service Info**: Ask "Service times"
- **Connect to Admin**: Say "I need help" or "talk to admin"

### Bible API
The chatbot uses https://bible-api.com/ (FREE, no API key needed)

Example requests:
- "John 3:16"
- "Psalm 23"
- "1 Corinthians 13:4-7"

### Profanity Filter
Automatically filters bad words in:
- User-to-user chat (chat.blade.php)
- Member chat (userchat.blade.php)
- Chatbot messages

### Adding More Profanity Words
```php
use App\Models\ProfanityWord;

ProfanityWord::create([
    'word' => 'badword',
    'language' => 'en' // or 'fil' for Filipino
]);
```

## Files Created
- `app/Http/Controllers/ChatbotController.php` - Main chatbot logic
- `app/Services/ProfanityFilter.php` - Profanity filtering service
- `app/Models/ProfanityWord.php` - Profanity word model
- `database/migrations/*_create_profanity_words_table.php` - Database table
- `database/seeders/ProfanityWordsSeeder.php` - Initial word list
- `app/Console/Commands/ImportProfanityWords.php` - Import command
- `resources/views/member/chatbot.blade.php` - Chatbot UI

## Cost
✅ **100% FREE** - No API keys or paid services required!
