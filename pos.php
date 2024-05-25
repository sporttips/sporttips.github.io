<?php 

/** 
 * Simple Brill transformational Part of Speech tagger
 * 
 * @author Ian Barber http://phpir.com
 */
class PosTagger {
	private $dict; 
	
	/**
	 * Construct the object, and store the contents of the lexicon. 
	 * The lexicon file should be a list of words then series of tags:
	 *
	 * word NN NNS 
	 * 
	 * @param string path to lexicon file 
	 */
	public function __construct($lexicon) {
		$fh = fopen($lexicon, 'r');
		while($line = fgets($fh)) {
			$tags = explode(' ', $line);
			$this->dict[strtolower(array_shift($tags))] = $tags;
			unset($tags);
		}
		unset($line);
		fclose($fh);
	}
	
	/**
	 * Tag the supplied text based on the Brill rules and the 
	 * values in the lexicon file. 
	 * 
	 * @param array a set of tokens to tag
	 * @return array a set of tags, with ids equal to the token ids
	 */
	public function tag(array $match) {
		$nouns = array('NN', 'NNS');
		$count = count($match);
		for($i = 0; $i < $count; $i++) {
			$token = $match[$i]; 
			
			// default to a common noun
			$return[$i] = 'NN';	

			// remove trailing full stops
			if(substr($token, -1) == '.') {
				$token = preg_replace('/\.+$/', '', $token);
			}
			
			// get from dict if set
			if(isset($this->dict[strtolower($token)])) {
				$return[$i] = $this->dict[strtolower($token)][0];
				$return[$i] = str_replace("\n", "", $return[$i]);
			}	
			
			// Converts verbs after 'the' to nouns
			if($i > 0) {
				if($return[$i - 1] == 'DT' && 
					in_array($return[$i], array('VBD', 'VBP', 'VB'))) {
					$return[$i] = 'NN';
				}
			}
			
			// Convert noun to number if . appears
			if($return[$i][0] == 'N' && strpos($token, '.') !== false) {
				$return[$i] = 'CD';
			}
			
			
			// Convert noun to past particile if ends with 'ed'
			if($return[$i][0] == 'N' && substr($token, -2) == 'ed') {
				$return[$i] = 'VBN';
			}
			
			// Anything that ends 'ly' is an adverb
			if(substr($token, -2) == 'ly') {
				$return[$i] = 'RB';
			}
			
			// Common noun to adjective if it ends with al
			if(in_array($return[$i], $nouns) && substr($token, -2) == 'al') {
				$return[$i] = 'JJ';
			}
			
			// Noun to verb if the word before is 'would'
			if($i > 0) {
				if($return[$i] == 'NN' && strtolower($match[$i-1]) == 'would') {
					$return[$i] = 'VB';
				}
			}
			
			// Convert noun to plural if it ends with an s
			if($return[$i] == 'NN' && substr($token, -1) == 's') {
				$return[$i] = 'NNS';
			}
			
			// Convert common noun to gerund
			if(in_array($return[$i], $nouns) && substr($token, -3) == 'ing') {
				$return[$i] = 'VBG';
			}
			
			// If we get noun noun, and the second can be a verb, then convert to verb
			if($i > 0) {
				if(in_array($return[$i], $nouns) && in_array($return[$i-1], $nouns) && isset($this->dict[strtolower($token)])) {
					if(in_array('VBN', $this->dict[strtolower($token)])) {
						$return[$i] = 'VBN';
					} else if(in_array('VBZ', $this->dict[strtolower($token)])) {
						$return[$i] = 'VBZ';
					}
				}
			}
		}
	
		return $return;
	}
}

