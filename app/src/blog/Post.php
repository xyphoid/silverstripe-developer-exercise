<?php 
namespace Xyphoid\DeveloperExercise\Blog;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use League\CommonMark\CommonMarkConverter;

class Post extends DataObject {

    private static $table_name = 'Post';

    // Note we don't know max size for any of these - we could guess that title is short 
    // but i'll just got Text for now. 
    private static $db = [
        'Title' => 'Text',
        'Author' => 'Text',
        'Slug' => 'Text',
        'Content' => 'Text'
    ];

    // not doing anything in Silverstripe admin at the mo but for later
    private static $summary_fields = [
        'Title' => 'Title',
        'Slug' => 'Caption'        
    ];

    public function getURL() {
        return '/posts/'.$this->Slug;
    }

    public function loadFrom($file) {
            // Remove .md suffix from markdown file for slug    
            // (actually did this before noticing slug in the file contents hah - 'The file name is the URL slug.' is not quite true!
            // $this->Slug = preg_replace('.md','',basename($file));

            // Pull title/author/slug. Validation would be nice here 
            // i don't recognise the === format - might be a parser for it but for now 
            // just split file by lines, grab data from lines 1/2/3, and treat lines 5+ as markdown content            
            $file = file($file); 

            preg_match("#Title: (.+?)\n#", $file[1], $res);
            $this->Title = $res[1];
            preg_match("#Author: (.+?)\n#", $file[2], $res);            
            $this->Author = $res[1];
            preg_match("#Slug: (.+?)\n#", $file[3], $res);
            $this->Slug = $res[1];
            
            // delete first 4 lines
            foreach(range(0,4) as $i) {
                unset($file[$i]);
            }

            // and reassemble the rest of the file
            $this->Content = implode($file); // file() left the \n on here
    }

    public function getTags() {
        // hmm - let's convert to html and use strip-tags to remove markdown
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // stopwords - low on time here
        $stopWords = [
            '#', '##', 'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am',
            'an', 'and', 'any', 'are', 'aren\'t', 'as', 'at', 'be', 'because', 'been',
            'before', 'being', 'below', 'between', 'both', 'but', 'by', 'can\'t', 'cannot',
            'could', 'couldn\'t', 'did', 'didn\'t', 'do', 'does', 'doesn\'t', 'doing', 'don\'t',
            'down', 'during', 'each', 'few', 'for', 'from', 'further', 'had', 'hadn\'t',
            'has', 'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'d', 'he\'ll', 'he\'s',
            'her', 'here', 'here\'s', 'hers', 'herself', 'him', 'himself', 'his', 'how',
            'how\'s', 'i', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'if', 'in', 'into', 'is', 'isn\'t',
            'it', 'it\'s', 'its', 'itself', 'let\'s', 'me', 'more', 'most', 'mustn\'t', 'my',
            'myself', 'no', 'nor', 'not', 'of', 'off', 'on', 'once', 'only', 'or', 'other',
            'ought', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 'same', 'shan\'t', 'she',
            'she\'d', 'she\'ll', 'she\'s', 'should', 'shouldn\'t', 'so', 'some', 'such', 'than', 'that',
            'that\'s', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'there', 'there\'s',
            'these', 'they', 'they\'d', 'they\'ll', 'they\'re', 'they\'ve', 'this', 'those', 'through',
            'to', 'too', 'under', 'until', 'up', 'very', 'was', 'wasn\'t', 'we', 'we\'d', 'we\'ll',
            'we\'re', 'we\'ve', 'were', 'weren\'t', 'what', 'what\'s', 'when', 'when\'s', 'where',
            'where\'s', 'which', 'while', 'who', 'who\'s', 'whom', 'why', 'why\'s', 'with', 'won\'t',
            'would', 'wouldn\'t', 'you', 'you\'d', 'you\'ll', 'you\'re', 'you\'ve', 'your', 'yours',
            'yourself', 'yourselves'
          ];
          
        try { 
            $string = strip_tags($converter->convertToHtml(strtolower($this->Content)));        
        } catch (\Exception $e) {
            // Turns out this converter doesn't handle content in telling-the-story-through-graphic-design
            return "[Cannot build tags]";
        }

        // extract words (lowercased and hopefully stripped of tags/markdown)
        // note that \w is missing both quotes, there are probably others to consider here
        preg_match_all('#([\w\'’-]+)#',$string, $res);
        $words = $res[0];

        // remove stopwords 
        $words = array_diff($words, $stopWords );

        // count the words 
        $counts = [];
        foreach($words as $key=>$word) {
            if(!isset($counts[$word])) {
                $counts[$word] = 0;
            }
            $counts[$word]++;
        }

        // sort by value (eg count)
        arsort($counts);        
    
        return implode(', ', array_keys(array_slice($counts,0,5)));


    }




}
