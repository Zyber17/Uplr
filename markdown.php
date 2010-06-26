<?php
#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# PHP Markdown
# Copyright (c) 2004-2009 Michel Fortin  
# <http://michelf.com/projects/php-markdown/>
#
# Original Markdown
# Copyright (c) 2004-2006 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#


define( 'MARKDOWN_VERSION',  "1.0.1n" ); # Sat 10 Oct 2009


#
# Global default settings:
#

# Change to ">" for HTML output
@define( 'MARKDOWN_EMPTY_ELEMENT_SUFFIX',  " />");

# Define the width of a tab for code blocks.
@define( 'MARKDOWN_TAB_WIDTH',     4 );


#
# WordPress settings:
#

# Change to false to remove Markdown from posts and/or comments.
@define( 'MARKDOWN_WP_POSTS',      true );
@define( 'MARKDOWN_WP_COMMENTS',   true );



### Standard Function Interface ###

@define( 'MARKDOWN_PARSER_CLASS',  'Markdown_Parser' );

function Markdown($text) {
#
# Initialize the parser and return the result of its transform method.
#
	# Setup static parser variable.
	static $parser;
	if (!isset($parser)) {
		$parser_class = MARKDOWN_PARSER_CLASS;
		$parser = new $parser_class;
	}

	# Transform text using parser.
	return $parser->transform($text);
}


### WordPress Plugin Interface ###

/*
Plugin Name: Markdown
Plugin URI: http://michelf.com/projects/php-markdown/
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://michelf.com/projects/php-markdown/">More...</a>
Version: 1.0.1n
Author: Michel Fortin
Author URI: http://michelf.com/
*/

if (isset($wp_version)) {
	# More details about how it works here:
	# <http://michelf.com/weblog/2005/wordpress-text-flow-vs-markdown/>
	
	# Post content and excerpts
	# - Remove WordPress paragraph generator.
	# - Run Markdown on excerpt, then remove all tags.
	# - Add paragraph tag around the excerpt, but remove it for the excerpt rss.
	if (MARKDOWN_WP_POSTS) {
		remove_filter('the_content',     'wpautop');
        remove_filter('the_content_rss', 'wpautop');
		remove_filter('the_excerpt',     'wpautop');
		add_filter('the_content',     'Markdown', 6);
        add_filter('the_content_rss', 'Markdown', 6);
		add_filter('get_the_excerpt', 'Markdown', 6);
		add_filter('get_the_excerpt', 'trim', 7);
		add_filter('the_excerpt',     'mdwp_add_p');
		add_filter('the_excerpt_rss', 'mdwp_strip_p');
		
		remove_filter('content_save_pre',  'balanceTags', 50);
		remove_filter('excerpt_save_pre',  'balanceTags', 50);
		add_filter('the_content',  	  'balanceTags', 50);
		add_filter('get_the_excerpt', 'balanceTags', 9);
	}
	
	# Comments
	# - Remove WordPress paragraph generator.
	# - Remove WordPress auto-link generator.
	# - Scramble important tags before passing them to the kses filter.
	# - Run Markdown on excerpt then remove paragraph tags.
	if (MARKDOWN_WP_COMMENTS) {
		remove_filter('comment_text', 'wpautop', 30);
		remove_filter('comment_text', 'make_clickable');
		add_filter('pre_comment_content', 'Markdown', 6);
		add_filter('pre_comment_content', 'mdwp_hide_tags', 8);
		add_filter('pre_comment_content', 'mdwp_show_tags', 12);
		add_filter('get_comment_text',    'Markdown', 6);
		add_filter('get_comment_excerpt', 'Markdown', 6);
		add_filter('get_comment_excerpt', 'mdwp_strip_p', 7);
	
		global $mdwp_hidden_tags, $mdwp_placeholders;
		$mdwp_hidden_tags = explode(' ',
			'<p> </p> <pre> </pre> <ol> </ol> <ul> </ul> <li> </li>');
		$mdwp_placeholders = explode(' ', str_rot13(
			'pEj07ZbbBZ U1kqgh4w4p pre2zmeN6K QTi31t9pre ol0MP1jzJR '.
			'ML5IjmbRol ulANi1NsGY J7zRLJqPul liA8ctl16T K9nhooUHli'));
	}
	
	function mdwp_add_p($text) {
		if (!preg_match('{^$|^<(p|ul|ol|dl|pre|blockquote)>}i', $text)) {
			$text = '<p>'.$text.'</p>';
			$text = preg_replace('{\n{2,}}', "</p>\n\n<p>", $text);
		}
		return $text;
	}
	
	function mdwp_strip_p($t) { return preg_replace('{</?p>}i', '', $t); }

	function mdwp_hide_tags($text) {
		global $mdwp_hidden_tags, $mdwp_placeholders;
		return str_replace($mdwp_hidden_tags, $mdwp_placeholders, $text);
	}
	function mdwp_show_tags($text) {
		global $mdwp_hidden_tags, $mdwp_placeholders;
		return str_replace($mdwp_placeholders, $mdwp_hidden_tags, $text);
	}
}


### bBlog Plugin Info ###

function identify_modifier_markdown() {
	return array(
		'name'			=> 'markdown',
		'type'			=> 'modifier',
		'nicename'		=> 'Markdown',
		'description'	=> 'A text-to-HTML conversion tool for web writers',
		'authors'		=> 'Michel Fortin and John Gruber',
		'licence'		=> 'BSD-like',
		'version'		=> MARKDOWN_VERSION,
		'help'			=> '<a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://michelf.com/projects/php-markdown/">More...</a>'
	);
}


### Smarty Modifier Interface ###

function smarty_modifier_markdown($text) {
	return Markdown($text);
}


### Textile Compatibility Mode ###

# Rename this file to "classTextile.php" and it can replace Textile everywhere.

if (strcasecmp(substr(__FILE__, -16), "classTextile.php") == 0) {
	# Try to include PHP SmartyPants. Should be in the same directory.
	@include_once 'smartypants.php';
	# Fake Textile class. It calls Markdown instead.
	class Textile {
		function TextileThis($text, $lite='', $encode='') {
			if ($lite == '' && $encode == '')    $text = Markdown($text);
			if (function_exists('SmartyPants'))  $text = SmartyPants($text);
			return $text;
		}
		# Fake restricted version: restrictions are not supported for now.
		function TextileRestricted($text, $lite='', $noimage='') {
			return $this->TextileThis($text, $lite);
		}
		# Workaround to ensure compatibility with TextPattern 4.0.3.
		function blockLite($text) { return $text; }
	}
}



#
# Markdown Parser Class
#

class Markdown_Parser {

	# Regex to match balanced [brackets].
	# Needed to insert a maximum bracked depth while converting to PHP.
	var $nested_brackets_depth = 6;
	var $nested_brackets_re;
	
	var $nested_url_parenthesis_depth = 4;
	var $nested_url_parenthesis_re;

	# Table of hash values for escaped characters:
	var $escape_chars = '\`*_{}[]()>#+-.!';
	var $escape_chars_re;

	# Change to ">" for HTML output.
	var $empty_element_suffix = MARKDOWN_EMPTY_ELEMENT_SUFFIX;
	var $tab_width = MARKDOWN_TAB_WIDTH;
	
	# Change to `true` to disallow markup or entities.
	var $no_markup = false;
	var $no_entities = false;
	
	# Predefined urls and titles for reference links and images.
	var $predef_urls = array();
	var $predef_titles = array();


	function Markdown_Parser() {
	#
	# Constructor function. Initialize appropriate member variables.
	#
		$this->_initDetab();
		$this->prepareItalicsAndBold();
	
		$this->nested_brackets_re = 
			str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
			str_repeat('\])*', $this->nested_brackets_depth);
	
		$this->nested_url_parenthesis_re = 
			str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
			str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);
		
		$this->escape_chars_re = '['.preg_quote($this->escape_chars).']';
		
		# Sort document, block, and span gamut in ascendent priority order.
		asort($this->document_gamut);
		asort($this->block_gamut);
		asort($this->span_gamut);
	}


	# Internal hashes used during transformation.
	var $urls = array();
	var $titles = array();
	var $html_hashes = array();
	
	# Status flag to avoid invalid nesting.
	var $in_anchor = false;
	
	
	function setup() {
	#
	# Called before the transformation process starts to setup parser 
	# states.
	#
		# Clear global hashes.
		$this->urls = $this->predef_urls;
		$this->titles = $this->predef_titles;
		$this->html_hashes = array();
		
		$in_anchor = false;
	}
	
	function teardown() {
	#
	# Called after the transformation process to clear any variable 
	# which may be taking up memory unnecessarly.
	#
		$this->urls = array();
		$this->titles = array();
		$this->html_hashes = array();
	}


	function transform($text) {
	#
	# Main function. Performs some preprocessing on the input text
	# and pass it through the document gamut.
	#
		$this->setup();
	
		# Remove UTF-8 BOM and marker character in input, if present.
		$text = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $text);

		# Standardize line endings:
		#   DOS to Unix and Mac to Unix
		$text = preg_replace('{\r\n?}', "\n", $text);

		# Make sure $text ends with a couple of newlines:
		$text .= "\n\n";

		# Convert all tabs to spaces.
		$text = $this->detab($text);

		# Turn block-level HTML blocks into hash entries
		$text = $this->hashHTMLBlocks($text);

		# Strip any lines consisting only of spaces and tabs.
		# This makes subsequent regexen easier to write, because we can
		# match consecutive blank lines with /\n+/ instead of something
		# contorted like /[ ]*\n+/ .
		$text = preg_replace('/^[ ]+$/m', '', $text);

		# Run document gamut methods.
		foreach ($this->document_gamut as $method => $priority) {
			$text = $this->$method($text);
		}
		
		$this->teardown();

		return $text . "\n";
	}
	
	var $document_gamut = array(
		# Strip link definitions, store in hashes.
		"stripLinkDefinitions" => 20,
		
		"runBasicBlockGamut"   => 30,
		);


	function stripLinkDefinitions($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: ^[id]: url "optional title"
		$text = preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\][ ]?:	# id = $1
							  [ ]*
							  \n?				# maybe *one* newline
							  [ ]*
							(?:
							  <(.+?)>			# url = $2
							|
							  (\S+?)			# url = $3
							)
							  [ ]*
							  \n?				# maybe one newline
							  [ ]*
							(?:
								(?<=\s)			# lookbehind for whitespace
								["(]
								(.*?)			# title = $4
								[")]
								[ ]*
							)?	# title is optional
							(?:\n+|\Z)
			}xm',
			array(&$this, '_stripLinkDefinitions_callback'),
			$text);
		return $text;
	}
	function _stripLinkDefinitions_callback($matches) {
		$link_id = strtolower($matches[1]);
		$url = $matches[2] == '' ? $matches[3] : $matches[2];
		$this->urls[$link_id] = $url;
		$this->titles[$link_id] =& $matches[4];
		return ''; # String that will replace the block
	}


	function hashHTMLBlocks($text) {
		if ($this->no_markup)  return $text;

		$less_than_tab = $this->tab_width - 1;

		# Hashify HTML blocks:
		# We only want to do this for block-level HTML tags, such as headers,
		# lists, and tables. That's because we still want to wrap <p>s around
		# "paragraphs" that are wrapped in non-block-level tags, such as anchors,
		# phrase emphasis, and spans. The list of tags we're looking for is
		# hard-coded:
		#
		# *  List "a" is made of tags which can be both inline or block-level.
		#    These will be treated block-level when the start tag is alone on 
		#    its line, otherwise they're not matched here and will be taken as 
		#    inline later.
		# *  List "b" is made of tags which are always block-level;
		#
		$block_tags_a_re = 'ins|del';
		$block_tags_b_re = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|'.
						   'script|noscript|form|fieldset|iframe|math';

		# Regular expression for the content of a block tag.
		$nested_tags_level = 4;
		$attr = '
			(?>				# optional tag attributes
			  \s			# starts with whitespace
			  (?>
				[^>"/]+		# text outside quotes
			  |
				/+(?!>)		# slash not followed by ">"
			  |
				"[^"]*"		# text inside double quotes (tolerate ">")
			  |
				\'[^\']*\'	# text inside single quotes (tolerate ">")
			  )*
			)?	
			';
		$content =
			str_repeat('
				(?>
				  [^<]+			# content without tag
				|
				  <\2			# nested opening tag
					'.$attr.'	# attributes
					(?>
					  />
					|
					  >', $nested_tags_level).	# end of opening tag
					  '.*?'.					# last level nested tag content
			str_repeat('
					  </\2\s*>	# closing nested tag
					)
				  |				
					<(?!/\2\s*>	# other tags with a different name
				  )
				)*',
				$nested_tags_level);
		$content2 = str_replace('\2', '\3', $content);

		# First, look for nested blocks, e.g.:
		# 	<div>
		# 		<div>
		# 		tags for inner block must be indented.
		# 		</div>
		# 	</div>
		#
		# The outermost tags must start at the left margin for this to match, and
		# the inner nested divs must be indented.
		# We need to do this before the next, more liberal match, because the next
		# match will start at the first `<div>` and stop at the first `</div>`.
		$text = preg_replace_callback('{(?>
			(?>
				(?<=\n\n)		# Starting after a blank line
				|				# or
				\A\n?			# the beginning of the doc
			)
			(						# save in $1

			  # Match from `\n<tag>` to `</tag>\n`, handling nested tags 
			  # in between.
					
						[ ]{0,'.$less_than_tab.'}
						<('.$block_tags_b_re.')# start tag = $2
						'.$attr.'>			# attributes followed by > and \n
						'.$content.'		# content, support nesting
						</\2>				# the matching end tag
						[ ]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document

			| # Special version for tags of group a.

						[ ]{0,'.$less_than_tab.'}
						<('.$block_tags_a_re.')# start tag = $3
						'.$attr.'>[ ]*\n	# attributes followed by >
						'.$content2.'		# content, support nesting
						</\3>				# the matching end tag
						[ ]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document
					
			| # Special case just for <hr />. It was easier to make a special 
			  # case than to make the other regex more complicated.
			
						[ ]{0,'.$less_than_tab.'}
						<(hr)				# start tag = $2
						'.$attr.'			# attributes
						/?>					# the matching end tag
						[ ]*
						(?=\n{2,}|\Z)		# followed by a blank line or end of document
			
			| # Special case for standalone HTML comments:
			
					[ ]{0,'.$less_than_tab.'}
					(?s:
						<!-- .*? -->
					)
					[ ]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
			
			| # PHP and ASP-style processor instructions (<? and <%)
			
					[ ]{0,'.$less_than_tab.'}
					(?s:
						<([?%])			# $2
						.*?
						\2>
					)
					[ ]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
					
			)
			)}Sxmi',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		return $text;
	}
	function _hashHTMLBlocks_callback($matches) {
		$text = $matches[1];
		$key  = $this->hashBlock($text);
		return "\n\n$key\n\n";
	}
	
	
	function hashPart($text, $boundary = 'X') {
	#
	# Called whenever a tag must be hashed when a function insert an atomic 
	# element in the text stream. Passing $text to through this function gives
	# a unique text-token which will be reverted back when calling unhash.
	#
	# The $boundary argument specify what character should be used to surround
	# the token. By convension, "B" is used for block elements that needs not
	# to be wrapped into paragraph tags at the end, ":" is used for elements
	# that are word separators and "X" is used in the general case.
	#
		# Swap back any tag hash found in $text so we do not have to `unhash`
		# multiple times at the end.
		$text = $this->unhash($text);
		
		# Then hash the block.
		static $i = 0;
		$key = "$boundary\x1A" . ++$i . $boundary;
		$this->html_hashes[$key] = $text;
		return $key; # String that will replace the tag.
	}


	function ha