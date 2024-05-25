<?php


/** 
 * Extension to the LangGen class that tries to extract a 
 * grammar model from the example text, then generate a 
 * grammar through random walk, and replace the grammar with
 * randomly chosen words that fit the part of speech and 
 * probability distribution. 
 *
 * @author Ian Barber http://phpir.com
 */
class PosGramGen extends LangGen { 
	private $tagger; 
	private $types;
	
	/**
	 * Constructor - can specify a lexicon for the PoS tagger. 
	 * The whole tagger could be passed in to make this a bit 
	 * cleaner!
	 */
	public function __construct($lexicon = 'data/lexicon.txt') {
		$this->tagger = new PosTagger($lexicon);
	}
	
	/**
	 * Tokenise and tag a string, but then store a list of the tags
	 * and tag pairs rather than string pairs. The mapping of tags 
	 * to the words that were tagged is stored in an array 'types'
	 * 
	 * @see LangGen::tokenise
	 * @param string to tokenise
	 * @return tags of string
	 */
	protected function tokenise($contents) {
		$tokens = parent::tokenise($contents);
		unset($contents);
		$tags = $this->tagger->tag($tokens);
		foreach($tags as $i => $tag) {
			if(!isset($this->types[$tag])) { 
				$this->types[$tag] = array();
			}
			if(!isset($this->types[$tag][$tokens[$i]])) {
				$this->types[$tag][$tokens[$i]] = 0;
			}
			
			$this->types[$tag][$tokens[$i]]++;
			$return[] = $tag;
		}
		unset($tokens);
		unset($tags);
		foreach($this->types as $key => $types) {
			$this->types[$key] = $this->probNormalise($types);
		}
		return $return;
	}
	
	/**
	 * Replace tags with randomly picked words that fitted
	 * that tag in the example text, but otherwise continue 
	 * as generateString in LangGen
	 * 
	 * @see LangGen::generateString
	 * @param array tags to generate string from 
	 * @return string generated string using words instead of tags
	 */
	protected function generateString(array $words) {
		foreach($words as $key => $tag) {
			$words[$key] = $this->pick($this->types[$tag]);
		}
		return parent::generateString($words);
	}
}

/**
 * Extension to the LangGen class that uses a part of speech
 * tagger to try and include more semantic structure. 
 * 
 * @author Ian Barber http://phpir.com
 */
class PosLangGen extends LangGen { 
	private $tagger; 
	
	/**
	 * Constructor - can specify a lexicon for the PoS tagger. 
	 * The whole tagger could be passed in to make this a bit 
	 * cleaner!
	 */
	public function __construct($lexicon = 'data/lexicon.txt') {
		$this->tagger = new PosTagger($lexicon);
	}
	
	/**
	 * Split a string up using the LangGen:tokenise 
	 * function, but then tag the tokens with the PoS
	 * tagger and return the tokens in the form 
	 * token/tag. 
	 * 
	 * @see LangGen::tokenise
	 * @see PosTagger::tag
	 * @param string to tokenise
	 * @return array tagged tokens
	 */
	protected function tokenise($contents) {
		$tokens = parent::tokenise($contents);
		$tags = $this->tagger->tag($tokens);
		foreach($tokens as $i => $token) {
			$return[] = $token . "/" . $tags[$i];
		}
		unset($tokens);
		unset($tags);
		return $return;
	}
	
	/**
	 * As the LangGen function, but strip the tag out
	 * from the token. 
	 * 
	 * @see LangGen::generateString
	 * @param array words to turn into a string
	 * @return string 
	 */
	protected function generateString(array $words) {
		foreach($words as $key => $word) {
			list($word, $tag) = explode("/", $word);
			$words[$key] = $word;
		}
		return parent::generateString($words);
	}
}

/**
 * Generate some nonsense text based on a previously seen example. 
 * 
 * Train with:
 * $l = new LangGen();
 * $l->learn('source.txt'); 
 * 
 * Strip tags is used, so HTML is fine. 
 * 
 * Generate text with 
 * $l->generate(100); 
 * 
 * First argument is length of text. 
 * 
 * @author Ian Barber http://phpir.com
 */
class LangGen {
	protected $model = array();
	protected $rootScores = array();
	protected $sentenceEnd = array('.', '!', '?');
	protected $joinSentence = array(',', ':', ';');

	/**
	 * Generate statistics from an example text or HTML file. 
	 * The argument is retrieved with file_get_contents so can
	 * be a URL. The result is run through strip tags, so can be
	 * HTML or XML. 
	 * 
	 * @param string filePath the location of the example file
	 */
	public function learn($filePath) {
		$contents = strip_tags(file_get_contents($filePath));
		$tokens = $this->tokenise($contents);
		unset($contents);
		
		$prevToken = null;
		foreach($tokens as $token) {
			if($prevToken) {
				if(!isset($this->model[$prevToken])) {
					$this->model[$prevToken] = array();
				}
				if(!isset($this->model[$prevToken][$token])) {
					$this->model[$prevToken][$token] = 0;
				}
				$this->model[$prevToken][$token]++;
			}
			$prevToken = $token;
			
			// handle sentence enders
			if(in_array($token, $this->sentenceEnd)) {
				$prevToken = null;
			} else {
				if(!isset($this->rootScores[$token])) {
					$this->rootScores[$token] = 0;
				}
				$this->rootScores[$token]++;
			}
		}
		unset($tokens);
		
		// normalise probabilities
		foreach($this->model as $key => $tokens) {
			$this->model[$key] = $this->probNormalise($tokens);
		}
		$this->rootScores = $this->probNormalise($this->rootScores);
	}
	
	/**
	 * Generate some nonsense text by executing a random walk
	 * over the data generated by the learn function. 
	 * 
	 * @param int length the length of text to generate, in word units.
	 */ 
	public function generate($length = 400) {
		if(!count($this->rootScores)) {
			return "Please train this class with learn() first\n";
		}
		
		$word = null;	
		for($i = 0; $i < $length; $i++) {
			if(is_array($this->model[$word])) {
				do {
					$return[$i] = $this->pick($this->model[$word]);
				} while($word == $return[$i]);
				$word = $return[$i];
			} else {
				$return[$i] = $word = $this->pick($this->rootScores);
			}
		}
		return $this->generateString($return);
	}	
	
	/**
	 * Prettify an array of words. Stick together sentences
	 * and clauses if full stops or commas have entries in the 
	 * words array, and uses ucwords after sentence enders and
	 * at the start. 
	 * 
	 * @param array word to be glued into a string
	 * @return the string
	 */
	protected function generateString(array $words) {
		$words[0] = ucwords($words[0]);
		foreach($words as $key => $word) {
			if(in_array($word, $this->sentenceEnd)) {
				$words[$key-1] .= $word;
				unset($words[$key]);
				$words[$key+1] = ucwords($words[$key+1]);
			} else if(in_array($word, $this->joinSentence)) {
				if(strlen($words[$key-1])) {
					$words[$key-1] .= $word;
				}
				unset($words[$key]);
			}
		}
		return implode(' ', $words);
	}
	
	/**
	 * Take an array of scores and normalise each one
	 * so that the total array runs from 0 - 1, with 
	 * the same distribution as with the scores. 
	 *  
	 * @param array of scores 
	 * @return array normalised 
	 */
	protected function probNormalise(array $array) {
		$total = array_sum($array);
		$runningScore = 0;
		foreach($array as $key => $score) {
			$runningScore += ($score/$total);
			$array[$key] = $runningScore; 
		}
		return $array;
	}

	/**
	 * Select a key from an array based on the generated
	 * float between 0 and 1 being less than the value
	 * associated with that key. 
	 * 
	 * @param array array of options
	 * @return string key on success, null on failure
	 */
	protected function pick(array $array) {
		$floatRand = rand(0, 1000000) / 1000000.0;
		foreach($array as $key => $value) {
			if($floatRand < $value) {
				return $key;
			}
		}
	}

	/**
	 * Split a string into tokens based on word characters or 
	 * some punctuation. Remove numbers and convert to lowercase. 
	 * 
	 * @param string the string to tokenise 
	 * @return array of tokens 
	 */
	protected function tokenise($string) {
		preg_match_all("/[\'|\w]+|[\:|\;|\.|\?|\!|\,]/", $string, $matches); 
		foreach($matches[0] as $id => $match) {
			if(is_numeric($match)) {
				unset($matches[0][$id]);
			} else {
				$matches[0][$id] = strtolower($match);
			}
		}
		return $matches[0]; 
	}
}
