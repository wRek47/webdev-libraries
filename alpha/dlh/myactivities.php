<?php

/*

	DLH - My Portfolio > My Activities
	
	This engine is built around transforming the existing JSON data-models to SQL data-models
	Upgrades include:
		Hobby-Data Centralization
		Hobby Management Tools
		Blog/Sharing
		Blog Management Tools
		Trending Articles
		Hobby/Activity Search
	
	Features include:
		PUG/Markdown/BBCode Support
		Custom Apps
	
	Required Connections:
		MySQL, PDO
	
	Require Engines:
		Profile
		Events

*/

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
	
	public function comment() {
	
		global $pdo, $profile;
		
		$comment = new HBArticleComment;
		
		if (!$this->guests_can_comment): return false; endif;
		
		$comment->author = $profile->portfolio->name;
		
		$comment->body = $_POST['body'];
				
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