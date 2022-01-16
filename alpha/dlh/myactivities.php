<?php

/*

DLH - My Portfolio > My Activities

v0.2.1

This engine is built around transforming the existing JSON data-models to SQL data-models

Upgrades include:
	Hobby-Data Centralization
	Hobby Management Tools
	Blog/Sharing
	Blog Management Tools
	Trending Articles
	Hobby/Activity Search

Features include:
	JSON?/HTML/Markdown/BBCode Support
	Custom Apps

Required Connections:
	MySQL, PDO

Require Engines:
	Profile
	Events

*/

class HTML { public function read() { } }
class Parsedown { public function read() { } }

class HobbyBlog {

	public $articles_per_page = null;
	public $anonymous_comments = true;
	public $guests_can_comment = true;
	
	public $articles;
	
	public function __construct() {
	
        if (isset($_GET['sort_by'])):
		
            if ($_GET['sort_by'] == "trending"):
            
                $this->get_articles_trending();
            
            else:
            
                $this->get_articles_by_date();
            
            endif;
        
        else:
        
            $this->get_articles_by_date();
        
        endif;
	
	}
	
	public function share($to) {
	
		global $profile;
	
	}

    private function sanitize_body() {
    
        $result = false;

        if ($_POST['attributes']['use_bbcode']):
        
            $bbcode_tags = array();
            $html_tags = array();

            $result = str_replace($bbcode_tags, $html_tags, $_POST['body']);
        
        elseif ($_POST['attributes']['use_markdown']):
        
            $markdown = new Parsedown;
            $result = $markdown->read($_POST['body']);
        
        elseif ($_POST['attributes']['use_html']):
        
            $html = new HTML;
            $result = $html->read($_POST['body']);
        
        elseif ($_POST['attributes']['use_json']):
        
            /* $json = new JSON;
            $result = $json->read($_POST['body']); */

            /* > */
            define("PHP_TAB", "\t");
            define("PHP_TAB1", "\t");
            define("PHP_TAB2", "\t\t");
            define("PHP_TAB3", "\t\t\t");
            define("PHP_TAB4", "\t\t\t\t");

            $data_object = json_decode($_POST['body']);
            $body = '<section>' . PHP_EOL;

            foreach ($data_object as $key => $value):
            
                if (is_object($value)):
                
                    $body .= PHP_TAB . '<h1>' . $key . '</h1>';
                    foreach ($value as $key2 => $value2):
                    
                        // repeat through h6
                    
                    endforeach; unset($key2, $value2);
                
                elseif (is_array($value)):
                
                    $first = reset($value);
                    $list = (is_numeric(key($first))) ? "ordered" : "unordered"; unset($first);
                    
                    if ($list == "ordered"): $body .= PHP_TAB1 . '<ol>' . PHP_EOL;
                    elseif ($list == "unordered"): $body .= PHP_TAB1 . '<ul>' . PHP_EOL;
                    elseif ($list == "definition"): $body .= PHP_TAB1 . '<dl>' . PHP_EOL;
                    endif;

                    foreach ($value as $key2 => $value2):
                    
                        // collapsible / dropdown list?
                        
                        if (is_object($value2)):
                        
                            $body .= PHP_TAB2 . '<li>' . PHP_EOL;
                            if ($list == "unordered"):
                                $body .= PHP_TAB3 . '<h3>' . $key2 . '</h3>' . PHP_EOL;
                            endif;
                            $body .= PHP_TAB3 . '<article>' . PHP_EOL;

                            foreach ($value2 as $key3 => $value3):
                            
                                $first = reset($value2);
                                $list2 = (is_numeric(key($key))) ? "ordered" : "unordered"; unset($first);
                                
                                if ($list == "unordered"):
                                    $body .= PHP_TAB4 . '<h3>' . $key3 . '</h3>' . PHP_EOL;
                                endif;

                                if (is_object($value3)):
                                elseif (is_array($value3)):
                                elseif (is_string($value3)):
                                
                                    $body .= PHP_TAB4 . '<p>' . $value3 . '</p>' . PHP_EOL;
                                
                                endif;
                            
                            endforeach; unset($key3, $value3);

                            $body .= PHP_TAB3 . '</article>' . PHP_EOL;
                            $body .= PHP_TAB2 . '</li>' . PHP_EOL;
                        
                        elseif (is_array($value2)):
                        
                        elseif (is_string($value2)):
                        
                            if (str_contains($list, "ordered")): $body .= PHP_TAB2 . '<li>' . PHP_EOL;
                            elseif ($list == "definition"): $body .= PHP_TAB2 . '<dd>' . PHP_EOL;
                            endif;

                            $body .= PHP_TAB2 . '<li>' . $value . '</li>' . PHP_EOL;

                            if (str_contains($list, "ordered")): $body .= PHP_TAB2 . '</li>' . PHP_EOL;
                            elseif ($list == "definition"): $body .= PHP_TAB2 . '</dd>' . PHP_EOL;
                            endif;
                        
                        endif;
                    
                    endforeach;
                    
                    if ($list == "ordered"): $body .= PHP_TAB1 . '</ol>' . PHP_EOL;
                    elseif ($list == "unordered"): $body .= PHP_TAB1 . '</ul>' . PHP_EOL;
                    elseif ($list == "definition"): $body .= PHP_TAB1 . '</dl>' . PHP_EOL;
                    endif;
                
                elseif (is_string($value)):
                
                    $body .= PHP_TAB1 . '<p>' . $value . '</p>' . PHP_EOL;
                
                endif;
            
            endforeach; unset($key, $value);

            $body .= '</section>' . PHP_EOL;
        
        else:
        
            $result = htmlspecialchars(trim($_POST['body']));
        
        endif;

        $safe = $result;

        return $safe;
    
    }
	
	public function comment() {
	
		global $pdo, $profile;
		
		$comment = new HBArticleComment;
		
		if (!$this->guests_can_comment): return false; endif;
		
		$comment->author = $profile->portfolio->name;
		$comment->body = $this->sanitize_body();
				
		$comment->timestamp = time();
				
		$comment->time = date("g.i a Z");
		$comment->date = date("F jS, Y");
		
		$sql = "INSERT INTO `hb_comments`(`author`, `body`, `timestamp`) VALUES(:author, :body, :timestamp)";
		$statement = $pdo->prepare($sql); unset($sql);
		
			$statement->bindParam("author", $comment->author);
			$statement->bindParam("body", $comment->body);
			$statement->bindParam("timestamp", $comment->timestamp);
		
		$result = $statement->fetch_result(); unset($statement);
		
		if ($result):
		
			# successful
		
		endif;
	
	}
	
	public function post() {
	
		global $pdo;
		
		$article = new HBArticle;
			$article->subject = $_POST['subject'];
			$article->description = $_POST['description'];
			$article->body = $_POST['body'];
		
		$sql = "INSERT INTO `hb_articles` (`subject`, `description`, `body`, `logs`) VALUES (:subject, :description, :body, :logs)";
		$statement = $pdo->prepare($sql); unset($sql);
		
			$statement->bindParam(":subject", $article->subject);
			$statement->bindParam(":description", $article->description);
			$statement->bindParam(":body", $article->body);
			$statement->bindParam(":logs", serialize($article->logs));
		
		$statement->execute();
		$result = $statement->fetch_result(); unset($statement);
		
		if ($result):
		
			# successful
		
		endif;
	
	}
	
	public function edit() { }
	
	private function get_articles_trending() { }
	private function get_articles_by_date() {
	
		global $pdo;
		
		$sql = "SELECT `subject, description, body, logs` FROM `hb_articles` ORDER BY `created` DESC";
		
		if (!is_null($this->articles_per_page)):
		
			$limit = $this->articles_per_page;
			
			$x = (isset($_GET['pid']) AND is_numeric($_GET['pid'])) ? $_GET['pid'] * $limit : 1;
			$y = $x + $limit;
			
			unset($limit);
			
			# limit articles in sql
			$sql .= " LIMIT BY :start, :end";
		
		endif;
		
        $statement = $pdo->prepare($sql);
		
		if (str_contains($sql, "LIMIT BY")):
		
			$statement->bindParam(":start", $x);
			$statement->bindParam(":end", $y);
		
		endif;
		
		$data = $statement->fetch_assoc();
		
		foreach ($data as $entry):
		
			$article = new HBArticle;
			
				$article->subject = $entry['subject'];
				$article->description = $entry['description'];
				$article->body = $entry['body'];
				$article->logs = unserialize($entry['logs']);
			
			$article->count_shares();
		
		endforeach; unset($entry, $data);
		
		array_push($this->articles, $article); unset($article);
	
	}

}

class HBArticleComment {

    public $author;
    public $body;

    public $timestamp;

    public $time;
    public $date;

}

class HBArticle {

	public $id;
	
	public $shares = 0;
	public $reads = 0;
	
	public function get_comments() {
	
        global $pdo;

		$sql = "SELECT `body`, `author`, `timestamp` FROM `hb_comments` WHERE `article` = :a_id";
		
		$statement = $pdo->prepare($sql); unset($sql);
			$statement->bindParam(":a_id", $this->id);
		$statement->execute();
		
		$data = $statement->fetch_assoc(); unset($statement);
		
		$comments = array();
		foreach ($data as $entry):
		
			$comment = new HBArticleComment;
			
				$comment->author = $entry['author'];
				$comment->body = $entry['body'];
				$comment->time = date("g.i a Z", $entry['timestamp']);
				$comment->date = date("F jS, Y", $entry['timestamp']);
			
			array_push($comments, $comment); unset($comment);
		
		endforeach; unset($entry, $data);
		
		$this->comments = $comments;
	
	}
	
	public function count_reads() {
	
		$i = 0;
		
		foreach ($this->logs as $stamp):
		
			if ($this->stamp->action == "read"): $i++; endif;
		
		endforeach; unset($stamp);
		
		$this->reads = $i;
	
	}
	
	public function count_shares() {
	
		$i = 0;
		
		foreach ($this->logs as $stamp):
		
			if ($this->stamp->action == "shared"): $i++; endif;
		
		endforeach; unset($stamp);
		
		$this->shares = $i; unset($i);
	
	}

}

class MyActivities {

	public $disciplines;
	public $hobbies = array();
	public $blog;
	
	public function __construct() {
	
		$this->get_hobbies();
		$this->get_disciplines();
		
		$this->blog = new HobbyBlog();
	
	}
	
	public function search() { }
	
	public function get_disciplines() {
	
		$disciplines = array();
		
		foreach ($this->hobbies as $hobby):
		
			array_push($disciplines, $hobby->discipline);
		
		endforeach; unset($hobby);
		
		sort($disciplines);
		$this->disciplines = array_unique($disciplines);
	
	}
	
	public function get_hobbies() {
	
		global $pdo;
		
		$sql = "SELECT `name`, `discipline`, `content`, `attributes` FROM `hobbies` ORDER BY `name` ASC";
		$statement = $pdo->prepare($sql); unset($sql);
			$statement->execute();
		
		$data = $statement->fetch_assoc(); unset($statement);
		
		$hobbies = array();
		
		foreach ($data as $article):
		
			$hobby = new Hobby;
			
				$hobby->name = $article['name'];
				$hobby->discipline = $article['discipline'];
				$hobby->content = $article['content'];
				$hobby->attributes = unserialize($article['attributes']);
				$hobby->logs = unserialize($article['logs']);
			
			array_push($hobbies, $hobby); unset($hobby);
		
		endforeach; unset($article, $data);
		
		$this->hobbies = $hobbies; unset($hobbies);
	
	}
	
	public function add_hobby() {
	
		global $pdo;
		
		$hobby = new Hobby;
			$hobby->name = $_POST['name'];
			$hobby->discipline = $_POST['discipline'];
			$hobby->content = $_POST['content'];
			$hobby->attributes = $_POST['attributes'];
		
		# secure $hobby values
		
		$sql = "INSERT INTO `hobbies` (`name`, `discipline`, `content`, `attributes`, `logs`) VALUES (:name, :discipline, :content, :attributes, :logs)";
		$statement = $pdo->prepare($sql); unset($sql);
		
			$statement->bindParam(":name", $hobby->name);
			$statement->bindParam(":discipline", $hobby->discipline);
			$statement->bindParam(":content", $hobby->content);
			$statement->bindParam(":attributes", serialize($hobby->attributes));
			$statement->bindParam(":logs", serialize($hobby->logs));
		
		$statement->execute();
		
		$query = $statement->fetch_result();
		if ($query):
		
			# successful
		
		endif;
	
	}

}

class Hobby {

	public $name;
	public $discipline;
	
	public $content;
	public $attributes;
	
	public $logs;
	
	public function update_hobby() {
	
		global $pdo;
		
		$hobby = new Hobby;
			$hobby->name = $_POST['name'];
			$hobby->discipline = $_POST['discipline'];
			$hobby->content = $_POST['content'];
			$hobby->attributes = $_POST['attributes'];
		
		# secure $hobby values
		
		$sql = "UPDATE `hobbies` SET `name` = :name, `discipline` = :discipline, `content` = :content, `attributes` = :attributes, `logs` = :logs";
		$statement = $pdo->prepare($sql); unset($sql);
		
			$statement->bindParam(":name", $hobby->name);
			$statement->bindParam(":discipline", $hobby->discipline);
			$statement->bindParam(":content", $hobby->content);
			$statement->bindParam(":attributes", serialize($hobby->attributes));
			$statement->bindParam(":logs", serialize($hobby->logs));
		
		$statement->execute();
		
		$query = $statement->fetch_result();
		if ($query):
		
			# successful
			$this->name = $hobby->name;
			$this->discipline = $hobby->discipline;
			$this->content = $hobby->content;
			$this->attributes = $hobby->attributes;
		
		endif;
		
	
	}

}

?>