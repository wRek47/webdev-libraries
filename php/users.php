<?php

if ($site->settings->open_registration):

    # $user = isset($biz) ? new UserAgent($biz) : new UserAgent;
    $user = new UserAgent;

else: $user = new GuestAgent; endif;

class UserAgent {

    public $vars;
    public $forms;

    public $users = [];
    public $developers = [];

    public $id = 0;
    public $cards;
    public $settings = null;

    public $components = null;

    public $is_registered = false;
    public $is_logged_in = false;
    public $has_access = false;
    public $access_proof = false;
    public $is_developer = false;

    public function __construct() {
    
        global $fw, $site, $biz;

        $this->forms = new FormAgent;
        $this->vars = (object) array();

            $this->vars->db = array();

                $this->vars->db['registered_users'] = "registered_users";
                $this->vars->db['online_users'] = "online_users";

                $this->vars->db['settings'] = "user_settings";
                $this->vars->db['notifications'] = "user_notifications";
            
                $this->vars->forms = array();
    
                $this->vars->session = array();
    
                    $this->vars->session['user_token'] = "profile_status";
            
            $this->load_users();
            $this->load_online_users();
    
            $this->load_profile_card();
            $this->auto_login_user();

            if ($this->is_logged_in):
            
                $security = json_decode($this->cards->profile->status->security);

                if ($security->level == "Critical"): $this->has_access = false; endif;
                if ($security->level == "Non-Critical"): $this->has_access = true; endif;
            
            endif;

            $this->notifications = $this->get_notifications();

            if ($this->has_access AND $this->access_proof):
            
                $this->cards->site = (object) array();
                $this->load_components();
            
            else:
            
                $this->is_developer = false;
            
            endif;
    
    }

    public function load_components() {
    
        if (!is_object($this->components)): $this->components = (object) array(); endif;

        if ($this->settings->filesys):
        
            $this->components->filesys = "user_filesys";
            $this->filesys = $this->load_filesys();
        
        endif;

        if ($this->settings->uploads):
        
            $this->components->uploads = "user_uploads";
            $this->uploads = $this->load_uploads();
        
        endif;

        if ($this->settings->calendar):
        
            $this->components->calendar = "user_calendar";
            $this->calendar = $this->load_calendar();
        
        endif;

        if ($this->settings->rolodex):
        
            $this->components->rolodex = "user_rolodex";
            $this->rolodex = $this->load_rolodex();
        
        endif;

        if ($this->settings->mailbox):
        
            $this->components->mailbox = "user_mailbox";
            $this->mailbox = $this->load_mailbox();
        
        endif;

        if ($this->settings->journal):
        
            $this->components->journal = "user_journal";
            $this->journal = $this->load_journal();
        
        endif;
    
    }

    public function load_profile_card() {
    
        $this->cards = (object) array();
        
        $settings = (object) array();

            $settings->remember_me = true;
            $settings->no_password = true;
            $settings->password = null;
            $settings->sfa_login = false;
            $settings->api_login = false;
            $settings->mfa_login = false;
            $settings->expiration = "3 days";
        
        $this->settings = $settings;

        $profile = (object) array();

            $profile->type = get_user_type();
            $profile->ip_address = get_user_ip();
            $profile->browser = get_user_browser();

            $profile->defcon = 5;

            # $profile->role = null;
            # $profile->session = null;

            # $profile->identity = "Guest";
            # $profile->method = "";

            $profile->status = $this->load_user_status();
        
        $this->cards->profile = $profile;
    
    }

    public function load_users() {
    
        $this->users = load_from_table($this->vars->db['registered_users']);

        foreach ($this->users as $user):
        
            if ($user->developer_card != ""): array_push($this->developers, $user); endif;
        
        unset($user); endforeach;
    
    }

    public function load_online_users() {
    
        $this->online_guests = [];
        $this->online_users = [];
        $this->online_developers = [];

        $auth_table = load_from_table($this->vars->db['online_users'], " ORDER BY `invoked` DESC");

        foreach ($auth_table as $row_id => $row):
        
            $found = array_query_id($this->online_users, $row->security, "security");
            if (!is_numeric($found)):
            
                $found = array_query_id($this->online_users, $row->code, "code");
                if (!is_numeric($found)):
                
                    $security = json_decode($row->security);
                    if ($security->auth == "Guest"):
                    
                        array_push($this->online_guests, $row);
                    
                    elseif ($security->auth == "Session"):
                    
                        $dev_status = $this->get_dev_status($row);

                        if ($dev_status):
                        
                            array_push($this->online_developers, $row);
                        
                        else:
                        
                            array_push($this->online_users, $row);
                        
                        endif;
                    
                    endif;
                
                endif;
            
            endif;
        
        unset($row_id, $row); endforeach;
    
    }

    public function get_dev_status($user_id, $strict = true) {
    
        if (is_numeric($user_id)):
        
            $dev = array_query($this->developers, $user_id, "user_id");
        
        else:
        
            $security = json_decode($user_id->security);
            if (isset($security->devcode)): $dev = array_query($this->developers, $security->devcode, "developer_card"); endif;

            # add user-id to dev-code verification
            if (!isset($dev)):
            
                $dev = array_query($this->developers, $_SESSION['dev_code'], "developer_card");
            
            endif;
        
        endif;

        if ($strict): return isset($dev) ? true : false;
        else: return isset($dev) ? $dev : false; endif;
    
    }

    public function get_online_status() {
    
        $result = array_query($this->online_users, $this->cards->profile->status->code, "code");
        if (!$result): $result = array_query($this->online_guests, $this->cards->profile->status->code, "code"); endif;
        if (!$result): $result = array_query($this->online_developers, $this->cards->profile->status->code, "code"); endif;

        return $result;
    
    }

    public function load_online_status() {
    
        $status = $this->get_online_status();

        if ($status):
        
            $status = $this->verify_online_status($status);

            if ($status):

                $query = array_query($this->users, $status->id, "online_id");
                $this->expected_id = $status->id;

                $this->assumed_id = 0;

                if ($query):
                
                    $this->is_registered = true;
                    $this->assumed_id = $query->id;
                
                endif;

                $security = json_decode($status->security);
                    $security->auth = "Session";

                # quantum-verify $_SESSION['dev_code'] and $security->devcode
                # and line #455 line #985?
                # this is hard to make right

                if (isset($security->devcode) AND $this->assumed_id):
        
                    if (isset($_SESSION['dev_code'])):
                    
                        if ($query->developer_card == $_SESSION['dev_code']):
                        
                            $this->is_developer = true;
                        
                        else:
                        
                            $this->is_developer = false;
                        
                        endif;
                    
                    else:
                    
                        if ($query->developer_card != ""):
                        
                            $dev_a = array_query($this->developers, $query->developer_card, "developer_card");
                            $dev_b = array_query($this->developers, $this->assumed_id, "id");

                            if ($dev_a == $dev_b):
                            
                                $_SESSION['dev_code'] = $query->developer_card;
                                $this->is_developer = true;
                            
                            else: $this->is_developer = false; endif;
                        
                        endif;
                    
                    endif;
                    /* $dev = $this->get_dev_status($this->id, false);
                    if ($dev):
                    
                        $security->devcode = $_SESSION['devcode'];
                        $_SESSION['dev_code'] = $security->devcode;
                    
                    else:
                    
                        if (isset($_SESSION['dev_code'])): $security->devcode = $_SESSION['dev_code'];
                        elseif (isset($security->devcode)): $_SESSION['dev_code'] = $security->devcode;
                        endif;
                        
                    endif; */
                    
                endif;
                
                if ($query AND isset($_SESSION['dev_code']) AND isset($security->devcode)):
                
                    if ($query->developer_card == $_SESSION['dev_code'] AND $query->developer_card == $security->devcode):
                    
                        $this->is_developer = true;
                    
                    endif;
                
                endif;

                $status->security = json_encode($security);
            
                $this->update_online_status($status);
            
            endif;
        
        else:
        
            $this->create_online_status();
        
        endif;

        $settings = [];

        if (isset($query)):
        
            if ($query): $settings = load_from_table($this->vars->db['settings'], " WHERE `user_id` = '{$query->id}'"); endif;
            if (!empty($settings)): $this->settings = $settings[0]; endif;

            $settings = $this->settings;

            if ($settings->remember_me):
            
                if ($settings->no_password):
                
                    $status->user_id = ($query) ? $query->id : $this->id;
                    $security->method = "Full Auto";

                    if (!isset($settings->id)):
                    
                        $this->cards->profile->defcon = 2;
                        $security->level = "Critical";
                    
                    else:
                    
                        $this->id = ($query) ? $query->id : $this->id;
                        $this->cards->profile->defcon = 3;
                        $security->level = "Non-Critical";
                    
                    endif;

                    $status->security = json_encode($security);
                
                endif;
            
            endif;
        
        endif;

        return $status;
    
    }

    public function create_online_status() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['online_users']}` (`code`, `session`, `invoked`, `expires`, `type`, `ip_address`, `browser`, `user_id`, `security`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("sssssssss", $code, $session, $invoked, $expires, $user_type, $ip_address, $browser, $user_id, $security);

        $session = $this->vars->session['user_token'];

        $start_date = date_create();
        $end_date = date_add(date_create(), date_interval_create_from_date_string($this->settings->expiration));

        $invoked = $start_date->format("Y-m-d H:i:s");
        $expires = $end_date->format("Y-m-d H:i:s");

        $user_type = $this->cards->profile->type;
        $user_type = "Human";
        $ip_address = $this->cards->profile->ip_address;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $browser = $this->cards->profile->browser;
        $browser = "n/a";

        $user_id = $this->id;
        $code = $this->cards->profile->status->code;

        $security = (object) array();
            if (!isset($this->cards->profile->auth)): $this->cards->profile->auth = "Guest"; endif;
            $security->auth = $this->cards->profile->auth;
        
        $security = json_encode($security);

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function update_online_status($status) {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['online_users']}` SET `code` = ?, `invoked` = ?, `expires` = ?, `security` = ?, `type` = ?, `ip_address` = ?, `browser` = ? WHERE `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("sssssssi", $code, $invoked, $expires, $security, $user_type, $ip_address, $browser, $id);

        $code = $status->code;

        $start_date = date_create();
        # $user = array_query($this->users, $online->identity, "");
        $end_date = extend_date($this->settings->expiration);

        $invoked = $start_date->format("Y-m-d H:i:s");
        $expires = $end_date->format("Y-m-d H:i:s");
        
        $user_type = $this->cards->profile->type;
        $user_type = "Human";
        $ip_address = $this->cards->profile->ip_address;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $browser = $this->cards->profile->browser;
        $browser = "N/a";

        $id = $this->id;

        $security = json_decode($status->security);
        if (is_null($security->auth)): $security->auth = "Guest"; endif;

        if (isset($this->expected_id)):
        
            if (isset($security->devcode)):
            
                $sql = "UPDATE `{$this->vars->db['registered_users']}` SET `developer_card` = ? WHERE `id` = ?";
                $pre1 = $db->prepare($sql); unset($sql);
                
                $pre1->bind_param("si", $dev_code, $user_id);

                $user_id = $this->expected_id;
                $dev_code = $security->devcode;

                $pre1->execute();

                if ($pre1->affected_rows > 0): $pre1 = true;
                else: $pre1 = false; endif;
            
            endif;

        endif;

        $security = json_encode($security);

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: false; endif;
    
    }

    public function verify_online_status($status) {
    
        $cmp1 = new DateTime();
        $cmp2 = isset($status->expires) ? new DateTime($status->expires) : $cmp1;

        if ($cmp1->getTimestamp() > $cmp2->getTimestamp()):
        
            $status = $this->expire_online_status($status);
        
        endif; unset($cmp1, $cmp2);

        $status = $this->invoke_online_status($status);

        return $status;
    
    }

    public function invoke_online_status($status) {
    
        $status->token = get_token($this->vars->session['user_token']);
        
        $expires = extend_date($this->settings->expiration);
        $status->expires = $expires->format("Y-m-d H:i:s");

        return $status;
    
    }

    public function expire_online_status($status) {
    
        $status->token = get_token($this->vars->session['user_token']);
        $status->expires = extend_date($this->settings->expiration);

        $status->identity = "Guest";
        $status->method = "";

        return $status;
    
    }

    public function auto_login_user() {
    
        $status = $this->load_online_status();
        $this->cards->profile->status = $status;

        $security = json_decode($status->security);

        if ($security->auth != "Guest"):
            
            if ($security->auth == "Session"):
                
                $credentials = (isset($security->method)) ? $security->method : null;

                if ($credentials == "Full Auto"):
                    
                    if (!isset($this->cards->profile->defcon)):
                        
                        $this->cards->profile->defcon = 1;
                        $security->level = "Critical";
                        
                    endif;
                    
                elseif (!is_null($credentials)):
                    
                    reset($credentials);

                    $key = key($credentials);
                    $user = array_query($this->users, $credentials->$key, $key); unset($key);

                    $this->id = $user->id;
                    $this->cards->profile->defcon = 3;
                    $security->level = "Non-Critical";
                    
                else:
                    
                    $this->cards->profile->defcon = 4;
                    $security->level = "Non-Critical";
                    
                endif;
                
            endif;
            
        else:
            
            $this->cards->profile->defcon = 5;
            $security->level = "Non-Critical";
            
        endif;

        $this->cards->profile->status->security = json_encode($security);
        if ($this->cards->profile->defcon >= 2): $this->is_logged_in = true; endif;
    
    }

    public function manual_login_user($credentials) {
    
        $result = false;

        # $this->cards->user->defcon = 4;

        return $result;
    
    }

    public function load_user_status() {
    
        $status = (object) array();

        $token = get_token($this->vars->session['user_token']);
        
        if ($token):
        
            $code = decode_token($token);
        
        else:
        
            $code = generate_code();
            $token = encode_token($code);
            set_token($this->vars->session['user_token'], $code);
        
        endif;

        $status->code = $code;
        $status->token = $token;

        return $status;
    
    }
    
    public function logout_user() { }

    public function email_user() { }
    public function sms_user() { }

    public function create_user() {
    
        global $db, $forms, $clock;

        $form = $forms['create_user'];

        $created = $clock->today;
        $online_id = $this->online_id;

        $sql = "INSERT INTO `{$this->vars->db['registered_users']}` (`online_id`, `created`) VALUES (?, ?)";
        $registration = $db->prepare($sql); unset($sql);

        $registration->bind_param("is", $online_id, $created);
        $registration->execute();

        if ($registration->affected_rows < 1): return false; endif;

        $user_id = $registration->insert_id;

        $sql = "INSERT INTO `{$this->vars->db['user_card']}` (`user_id`, `name`, `birthday`, `phone`, `email`, `company`, `address`, `facebook`, `twitter`, `linkedin`, `youtube`, `tiktok`, `github`, `webpages`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $ucard = $db->prepare($sql); unset($sql);

        $ucard->bind_param("isssssssssssss", $user_id, $name, $birthday, $phone, $email, $company, $address, $facebook, $twitter, $linkedin, $youtube, $tiktok, $github, $webpages);

            $name = $form->name;
            $birthday = $form->birthday;
            $phone = $form->phone;
            $email = $form->email;
            $company = $form->company;
            $address = $form->address;

            $facebook = $form->facebook;
            $twitter = $form->twitter;
            $linkedin = $form->linkedin;
            $youtube = $form->youtube;
            $tiktok = $form->tiktok;
            $github = $form->github;
            $webpages = $form->webpages;
        
        $ucard->execute();

        if (isset($this->biz)):
        
            $sql = "INSERT INTO `{$this->vars->db['customer_card']}` (`user_id`, `online_id`) VALUES (?, ?)";
            $ccard = $db->prepare($sql); unset($sql);
            $ccard->bind_param("ii", $user_id, $online_id);
            $ccard->execute();

            $sql = "INSERT INTO `{$this->vars->db['employee_card']}` (`user_id`, `online_id`, `created`) VALUES (?, ?, ?)";
            $ecard = $db->prepare($sql); unset($sql);
            $ecard->bind_param("iis", $user_id, $online_id, $created);
            $ecard->execute();
        
        endif;

        if ($user_id == 1):
        
            $sql = "INSERT INTO `{$this->vars->db['developer_card']}` (`user_id`, `online_id`, `created`) VALUES (?, ?, ?)";
            $dcard = $db->prepare($sql); unset($sql);
            $dcard->bind_param("iis", $user_id, $online_id, $created);
            $dcard->execute();
        
        endif;

        $sql = "UPDATE `{$this->vars->db['registered_user']}` SET `user_status` = ?, `customer_status` = ?, `employee_status` = ?, `developer_status` = ? WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("ssssi", $user_card, $customer_card, $employee_card, $developer_card, $user_id);

            $user_card = ($ucard->affected_rows > 0) ? $ucard->insert_id : 0;
            $customer_card = (isset($ccard) AND $ccard->affected_rows > 0) ? $ccard->insert_id : 0;
            $employee_card = (isset($ecard) AND $ecard->affected_rows > 0) ? $ecard->insert_id : 0;
            $developer_card = (isset($dcard) AND $dcard->affected_rows > 0) ? $dcard->insert_id : 0;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_user() {
    
        global $db, $forms;

        $form = $forms['edit_user'];

        $sql = "UPDATE `{$this->vars->db['registered_users']}` SET `online_id` = ?, `layout_card` = ?, `user_card` = ?, `customer_card` = ?, `employee_card` = ?, `developer_card` = ? WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("issssssi", $online_id, $layout_card, $user_card, $customer_card, $employee_card, $developer_card, $row_id);
        
            $online_id = $this->online_id;

            $layout_card = $this->cards->layout;
            $user_card = $this->cards->profile;
            $customer_card = $this->cards->customer;
            $employee_card = $this->cards->employee;
            $developer_card = $this->cards->developer;

            $row_id = $form->user_id;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_user($user_id = 0) {
    
        if ($this->id == 0): return false; endif;
        if ($user_id == 0): $user_id = $this->id; endif;
        
        $result = drop_from_table($this->vars->db['registered_users'], " `id` = '{$user_id}'");
        if ($result): $result = drop_from_table($this->vars->db['online_users'], " `user_id` = '{$user_id}'"); endif;

        return $result;
    
    }

    public function get_layout_card() { }

    public function get_notifications() {
    
        $notices = load_from_table($this->vars->db['notifications'], " WHERE `recipient` = 0");

        if ($this->is_logged_in):
        
            $user_notices = load_from_table($this->vars->db['notifications'], " WHERE `recipient` = '{$this->id}'");
            $notices = array_merge($notices, $user_notices);
        
        endif;

        return $notices;
    
    }

    public function send_notification() {
    
        global $db, $forms;

        $form = $forms['send_notification'];

        $sql = "INSERT INTO `{$this->vars->db['notifications']}` (`author`, `receipient`, `message`, `sticky`, `invoked`, `expires`) VALUES (?, ?, ?, ?, ?, ?)";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("iissss", $author, $recipient, $message, $sticky, $invoked, $expires);

        $author = $this->id;
        $recipient = $form->recipient;
        $message = $form->message;
        $sticky = $form->sticky;
        $invoked = $form->start;
        $expires = $form->expires;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_notification($id) {
    
        global $db, $forms;
        
        $form = $forms['drop_notification'];

        $sql = "UPDATE `{$this->vars->db['notifications']}` SET `dismissed` = ? WHERE `id` = ? AND `recipient` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("sii", $dismissed, $id, $recipient);
        
        $id = $form->row_id;
        $recipient = $form->user_id;
        $dismissed = $form->dismissed;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_card() {
    
        /* $card = (object) array();

            $card->name = "Guest";
            $card->birthdate = false;
            $card->email = false;
            $card->phone = false;
            $card->location = false; */
        
        $card = load_from_table($this->vars->db['cards'], " WHERE `user_id` = '{$this->id}'");
    
        return $card;
    
    }

    public function edit_card() {
    
        global $db, $forms;

        $form = $forms['edit_user_card'];

        $sql = "INSERT INTO `{$this->vars->db['card']}` (`name`, `birthdate`, `email`, `phone`) VALUES (?, ?, ?, ?)";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("ssss", $name, $birthdate, $email, $phone);

        $name = $form->name;
        $birthdate = $form->birthdate;
        $email = $form->email;
        $phone = $form->phone;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_contacts() {
    
        $dex = load_from_table($this->vars->db['rolodex'], " WHERE `user_id` = '{$this->id}'");

        return $dex;
    
    }

    public function add_contact() {
    
        global $db, $forms;

        $form = $forms['new_contact'];

        $sql = "INSERT INTO `{$this->vars->db['rolodex']}` (`user_id`, `notebook_id`, `event_id`, `journal_id`, `portfolio_id`, `tags`, `name`, `address`, `phone`, `social_media`, `company`, `relationship`, `description`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("sssssssssssss", $user_id, $notebook_id, $event_id, $journal_id, $portfolio_id, $tags, $name, $address, $phone, $social_media, $company, $relationship, $description);

        $user_id = $this->id;
        
        $notebook_id = $form->notebook_id;
        $event_id = $form->event_id;
        $journal_id = $form->journal_id;
        $portfolio_id = $form->portfolio_id;

        $tags = $form->tags;
        $name = $form->name;
        $address = $form->address;
        $phone = $form->phone;
        $social_media = $form->social_media;
        $company = $form->company;
        $relationship = $form->relationship;
        $description = $form->description;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_contact() {
    
        global $db, $forms;

        $form = $forms['edit_contact'];

        $sql = "UPDATE `{$this->vars->db['rolodex']}` SET `notebook_id` = ?, `event_id` = ?, `journal_id` = ?, `portfolio_id` = ?, `tags` = ?, `name` = ?, `address` = ?, `phone` = ?, `social_media` = ?, `company` = ?, `relationship` = ?, `description` = ? WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("ssssssssssssii", $notebook_id, $event_id, $journal_id, $portfolio_id, $tags, $name, $address, $phone, $social_media, $company, $relationship, $description, $contact_id, $user_id);

        $notebook_id = $form->notebook_id;
        $event_id = $form->event_id;
        $journal_id = $form->journal_id;
        $portfolio_id = $form->portfolio_id;
        $contact_id = $form->contact_id;
        $user_id = $this->id;
        
        $tags = $form->tags;
        $name = $form->name;
        $address = $form->address;
        $phone = $form->phone;
        $social_media = $form->social_media;
        $company = $form->company;
        $relationship = $form->relationship;
        $description = $form->description;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_contact() {
    
        global $db, $forms;

        $form = $forms['drop_contact'];

        $sql = "DELETE FROM `{$this->vars->db['rolodex']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("ii", $contact_id, $user_id);

        $contact_id = $form->contact_id;
        $user_id = $this->id;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_group_contacts($group) {
    
        $dex = $this->rolodex;

        return array_query_all($dex, $group, "group");
    
    }

    public function get_contact($list, $id) {
    
        $contact = array_query($list, $id, "id");
        return $contact;
    
    }

    public function get_events() {
    
        $events = load_from_table($this->vars->db['events'], " WHERE `user_id` = '{$this->id}'");

        return $events;
    
    }

    public function add_event() {
    
        global $db, $forms;

        $form = $forms['add_event'];

        $sql = "INSERT INTO `{$this->vars->db['events']}` (`notebook_id`, `event_id`, `journal_id`, `portfolio_id`) VALUES (?, ?, ?, ?)";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("iiii", $notebook_id, $event_id, $journal_id, $portfolio_id);

        $notebook_id = $form->notebook_id;
        $event_id = $form->event_id;
        $journal_id = $form->journal_id;
        $portfolio_id = $form->portfolio_id;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_event() {
    
        global $db, $forms;

        $form = $forms['edit_event'];

        $sql = "UPDATE `{$this->vars->db['events']}` SET `notebook_id` = ?, `event_id` = ?, `journal_id` = ?, `portfolio_id` = ? WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("iiii", $notebook_id, $event_id, $journal_id, $portfolio_id);

        $notebook_id = $form['notebook_id'];
        $event_id = $form['event_id'];
        $journal_id = $form['journal_id'];
        $portfolio_id = $form['portfolio_id'];

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function delete_event() {
    
        global $db, $forms;

        $form = $forms['delete_event'];

        $sql = "DELETE FROM `{$this->vars->db['events']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        $pre->bind_param("ii", $id, $user_id);

        $id = $form['event_id'];
        $user_id = $this->id;

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_tickets() {
    
        $tickets = load_from_table($this->vars->db['tickets'], " WHERE `user_id` = '{$this->id}'");
        
        return $tickets;
    
    }

    public function add_ticket() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['tickets']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_ticket() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['tickets']}` SET () WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_ticket() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['tickets']}` WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_uploads() {
    
        $uploads = load_from_table($this->vars->db['uploads'], " WHERE `user_id`= '{$this->id}'");

        return $uploads;
    
    }

    public function add_upload() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['uploads']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_upload() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['uploads']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_upload() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['uploads']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_comments() {
    
        $comments = load_from_table($this->vars->db['comments'], " WHERE `user_id` = '{$this->id}'");

        return $comments;
    
    }

    public function add_comment() {
    
        global $db, $forms;

        $sql = "INSERT INTO `{$this->vars->db['comments']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_comment() {
    
        global $db, $forms;

        $sql = "UPDATE `{$this->vars->db['comments']}` SET () WHERE `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_comment() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['comments']}`";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_reviews() {
    
        $reviews = load_from_table($this->vars->db['reviews'], " WHERE `user_id` = '{$this->id}'");

        return $reviews;
    
    }

    public function add_review() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['reviews']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_review() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['reviews']}` SET () WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_review() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['reviews']}` WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_ratings() {
    
        $ratings = load_from_table($this->vars->db['ratings'], " WHERE `user_id` = '{$this->id}'");

        return $ratings;
    
    }

    public function add_rating() {
    
        global $db, $forms;

        $sql = "INSERT INTO `{$this->vars->db['ratings']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_rating() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['ratings']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_rating() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['ratings']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_referrals() {
    
        $referrals = load_from_table($this->vars->db['referrals'], " WHERE `user_id` = '{$this->id}'");

        return $referrals;
    
    }

    public function add_referral() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['referrals']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_suggestions() {
    
        $suggestions = load_from_table($this->vars->db['suggestions'], " WHERE `user_id` = '{$this->id}'");

        return $suggestions;
    
    }

    public function add_suggestion() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['suggestions']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_wall_messages() {
    
        $wall = load_from_table($this->vars->db['wall'], " WHERE `user_id` = '{$this->id}'");

        return $wall;
    
    }

    public function add_wall_message() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['wall']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_wall_message() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['wall']}` SET () `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_wall_message() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['wall']}` WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_group_chatrooms() {
    
        $chatrooms = load_from_table($this->vars->db['group_messages']);

        return $chatrooms;
    
    }

    public function add_group_message() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['group_messages']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_group_message() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['group_messages']}` SET () WHERE `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_group_message() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['group_messages']}` WHERE `id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_forum_threads() {
    
        $threads = load_from_table($this->vars->db['forum_threads']);

        return $threads;
    
    }

    public function add_forum_thread() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['forum_threads']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_forum_thread() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['forum_threads']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_forum_thread() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['forum_threads']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_forum_posts() {
    
        $posts = load_from_table($this->vars->db['forum_posts']);
        
        return $posts;
    
    }

    public function add_forum_post() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['forum_posts']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_forum_post() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['forum_posts']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_forum_post() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['forum_posts']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_private_messages() {
    
        $messages = load_from_table($this->vars->db['private_messages'], " WHERE `user_id` = '{$this->id}'");

        return $messages;
    
    }

    public function add_private_message() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['private_messages']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_private_message() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['private_messages']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_private_message() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['private_messages']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_documents() {
    
        $document = load_from_table($this->vars->db['documents'], " WHERE `user_id` = '{$this->id}'");

        return $document;
    
    }

    public function add_document() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['documents']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_document() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['documents']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_document() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['documents']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }
    
    public function get_notebooks() {
    
        $notebooks = load_from_table($this->vars->db['notebooks'], " WHERE `user_id` = '{$this->id}'");

        return $notebooks;
    
    }

    public function add_notebook() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['notebooks']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_notebook() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['notebooks']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_notebook() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['notebooks']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_notes() {
    
        $notes = load_from_table($this->vars->db['notes'], " WHERE `user_id` = '{$this->id}'");

        return $notes;
    
    }

    public function add_note() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['notes']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_note() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['notes']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_note() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['notes']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_notebook_notes() { }

    public function get_lists() {
    
        $lists = load_from_table($this->vars->db['lists'], " WHERE `user_id` = '{$this->id}'");

        return $lists;
    
    }

    public function add_list() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['lists']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_list() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['lists']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_list() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['lists']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_notebook_lists() { }

    public function get_tasks() {
    
        $tasks = load_from_table($this->vars->db['tasks'], " WHERE `user_id` = '{$this->id}'");

        return $tasks;
    
    }

    public function add_task() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['tasks']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_task() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['tasks']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_task() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['tasks']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_notebook_tasks() { }

    public function get_albums() {
    
        $albums = load_from_table($this->vars->db['albums'], " WHERE `user_id` = '{$this->id}'");

        return $albums;
    
    }

    public function add_album() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['albums']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_album() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['albums']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_album() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['albums']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_notebook_albums() { }

    public function get_public_mail() {
    
        $mail = load_from_table($this->vars->db['global_mail'], " WHERE `user_id` = '{$this->id}'");

        return $mail;
    
    }

    public function get_private_mail() {
    
        $mail = load_from_table($this->vars->db['private_mail'], " WHERE `user_id` = '{$this->id}'");

        return $mail;
    
    }

    public function get_bookmarks() {
    
        $bookmarks = load_from_table($this->vars->db['bookmarks'], " WHERE `user_id` = '{$this->id}'");

        return $bookmarks;
    
    }

    public function add_bookmark() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['bookmarks']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_bookmark() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['bookmarks']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_bookmark() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['bookmarks']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_favorites() {
    
        $favorites = load_from_table($this->vars->db['favorites'], " WHERE `user_id` = '{$this->id}'");

        return $favorites;
    
    }

    public function add_favorite() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['favorites']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_favorite() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['favorites']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_favorite() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['favorites']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_timers() {
    
        $timers = load_from_table($this->vars->db['timers'], " WHERE `user_id` = '{$this->id}'");

        return $timers;
    
    }

    public function add_timer() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['timers']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_timer() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['timers']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_timer() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['timers']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();
        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_alarms() {
    
        $alarms = load_from_table($this->vars->db['alarms'], " WHERE `user_id` = '{$this->id}'");

        return $alarms;
    
    }

    public function add_alarm() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['alarms']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_alarm() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['alarms']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_alarm() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['alarms']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function get_clocks() {
    
        $clocks = load_from_table($this->vars->db['clocks'], " WHERE `user_id` = '{$this->id}'");

        return $clocks;
    
    }

    public function add_clock() {
    
        global $db;

        $sql = "INSERT INTO `{$this->vars->db['clocks']}` () VALUES ()";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function edit_clock() {
    
        global $db;

        $sql = "UPDATE `{$this->vars->db['clocks']}` SET () WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

    public function drop_clock() {
    
        global $db;

        $sql = "DELETE FROM `{$this->vars->db['clocks']}` WHERE `id` = ? AND `user_id` = ?";
        $pre = $db->prepare($sql); unset($sql);

        # $pre->bind_param();

        $pre->execute();

        if ($pre->affected_rows > 0): return true;
        else: return false; endif;
    
    }

}

?>
