// List of words to be filtered in church chat
const badWords = [
    // Profanity
    'damn', 'hell', 'ass', 'asshole', 'fuck', 'fucking', 'shit', 'bullshit', 'crap',
    'bitch', 'bastard', 'dick', 'penis', 'vagina', 'pussy', 'cock', 
    'whore', 'slut', 'hoe', 'cunt', 'nigger', 'nigga', 'fag', 'faggot',
    
    // Blasphemy
    'goddamn', 'goddam', 'jesus christ', 'christ sake', 'holy shit', 'holy fuck',
    
    // Sexual references
    'porn', 'pornography', 'sex', 'sexy', 'horny', 'orgasm', 'masturbate',
    
    // Drug references
    'weed', 'cocaine', 'heroin', 'meth', 'crack', 'marijuana', 'blunt', 'high',
    
    // Violence
    'kill', 'murder', 'suicide', 'rape', 'terrorist', 'bomb'
];

// Function to censor bad words in a string
function filterBadWords(text) {
    if (!text) return text;
    
    let filteredText = text;
    
    badWords.forEach(word => {
        // Create a regular expression that matches the whole word with word boundaries
        const regex = new RegExp('\\b' + word + '\\b', 'gi');
        
        // Replace the word with asterisks of the same length
        filteredText = filteredText.replace(regex, match => '*'.repeat(match.length));
    });
    
    return filteredText;
} 