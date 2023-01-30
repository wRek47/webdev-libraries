<?php

// always on
$system_ready = true;

$db = false;

# stamps and signatures
$stamp = (object) array();

    $stamp->target = "";
    $stamp->tags = "";

    $stamp->created = "";

    $stamp->title = "";
    $stamp->description = "";
    $stamp->message = "";

$stamps = [];

$sig = (object) array();

    $sig->target = "";

    $sig->title = "";
    $sig->description = "";
    $sig->stamps = $stamps;

$sig->stamp = $stamp;

# file system
$folder = (object) array();

    $folder->target = "";

    $folder->name = "";
    $folder->path = "";

    $folder->created = "";
    $folder->updated = "";
    $folder->permissions = "";
    $folder->size = "";

$file = (object) array();

    $file->target = "";

    $file->name = "";
    $file->path = "";
    $file->ext = "";
    $file->mime = "";

    $file->created = "";
    $file->updated = "";
    $file->permissions = "";
    $file->size = "";

$item = $file;

    $item->title = "";
    $item->description = "";

# html constructs (grid, inline, block, section, article, nav, list, link, form, input)
$grid = (object) array();

    $grid->top = [];
    $grid->header = [];
    $grid->offset = [];
    $grid->main = [];
    $grid->footer = [];
    $grid->bottom = [];

$role = (object) array();

    $role->human = true;
    $role->robot = false;
    $role->unknown = false;
    
    $role->level = 1;
    $role->defcon = 5;
    $role->title = "";

    $role->returning = false;
    $role->registered = false;
    $role->logged_in = false;
    $role->matching = true;

    $role->trusted = false;
    $role->verified = false;
    $role->proven = false;

$access = (object) array();

    $access->online_id = 0;

    $access->expected_id = 0;
    $access->assumed_id = 0;
    $access->proven_id = 0;

    $access->restricted = false;
    $access->denied = false;
    $access->limited = true;
    $access->granted = false;

$auth = (object) array();

    $auth->role = $role;
    $auth->access = $access;

    $auth->found = false;
    $auth->matching = false;
    $auth->verified = true;

$security = (object) array();

    $security->user_card = $auth;

$privacy = (object) array();

    $privacy->is_public = true;
    $privacy->is_sharing = false;
    $privacy->is_private = false;

$bootstrap = (object) array();

    $bootstrap->toggle = "";
    $bootstrap->target = "";

$inline = (object) array();

    $inline->id = "";
    $inline->class = "";

    $inline->text = "";

    $inline->bs = $bootstrap;

$block = (object) array();

    $block->heritage = "target";
    $block->parent = "";

    $block->id = "";
    $block->class = "";

    $block->title = "";
    $block->description = "";
    $block->body = $grid;

    $block->bs = $bootstrap;

$blocks = (object) array();

    $blocks->target = "";
    $blocks->content = [];
    $blocks->new = $block;

$panel = (object) array();

    $panel->position = "";

    $panel->id = "";
    $panel->class = "";

    $panel->title = "";
    $panel->description = "";
    $panel->body = "";

    $panel->bs = $bootstrap;

$panels = (object) array();

    $panels->target = "";
    $panels->content = [];
    $panels->new = $panel; 

$container = (object) array();

    $container->panels = [];
    $container->panel = $panel;

    $container->blocks = [];
    $container->block = $block;

    $container->id = "";
    $container->class = "";

    $container->bs = $bootstrap;

$containers = (object) array();

    $containers->target = "";
    $containers->content = [];
    $containers->new = $container;

$section = $block;

    $section->grid = $grid;

$aside = $block;

    $aside->header = "";
    $aside->cotent = "";

$article = $block;

    $article->header = "";
    $article->content = "";

$link = (object) array();

    $link->nav = "";
    $link->group = "";

    $link->text = "";
    $link->href = "";

    $link->bs = $bootstrap;

$links = (object) array();

    $links->target = "";
    $links->content = [];
    $links->new = $link;

$nav = $block;

    $nav->panel = "";
    $nav->type = "";
    $nav->order = "";
    $nav->let = false;

    $nav->header = "";
    $nav->items = [];
    $nav->links = [];
    $nav->link = $link;

    $nav->item = $block;

$range = (object) array();

    $range->min = "";
    $range->max = "";
    $range->value = "";
    $range->start = "";
    $range->limit = "";

    $range->prev = "";
    $range->current = "";
    $range->next = "";

$list = $nav;

    $list->range = $range;

    $list->citations = "";
    $list->references = "";

$list->item = (object) array();

    $list->item->value = "";

$lists = (object) array();

    $lists->target = "";
    $lists->content = [];
    $lists->new = $list;

$form = (object) array();

    $form->action = "";
    $form->method = "";

    $form->respond = false;
    $form->resolved = false;

    $form->fields = [];

$forms = (object) array();

    $forms->target = "";
    $forms->content = [];
    $forms->new = $form;

$button = $block;

    $button->type = "";
    $button->text = "";
    $button->name = "";
    $button->value = "";

$input = (object) array();

    $input->text = (object) array();

        $input->text->id = "";
        $input->text->class = "form-control";
        $input->text->type = "text";
        $input->text->name = "";
        $input->text->value = "";
        $input->text->placeholder = "";
    
    $input->select = (object) array();

        $input->select->id = "";
        $input->select->class = "form-control";
        $input->select->name = "";
        $input->select->options = [];

$input->select->option = (object) array();

    $input->select->option->id = "";
    $input->select->option->class = "";
    $input->select->option->text = "";
    $input->select->option->value = "";

// add more form field data models

$html = (object) array();

    $html->containers = $containers;
    $html->forms = $forms;
    $html->links = $links;
    $html->images = $images;
    $html->lists = $lists;
    $html->blocks = $blocks;

/* common items */

$note = $block;

    $note->book = "";

    $note->title = "";
    $note->body = "";
    $note->attachments = "";

    $note->citations = "";
    $note->references = "";

$definition = $block;

    $definition->topic = "";
    $definition->text = "";

    $definition->references = "";
    $definition->citations = "";

$glossary = $block;

    $glossary->content = [];
    $glossary->new = $definition;

$preface = $block;

    $preface->content = [];
    $preface->new = $note;

$contents = $block;

    $contents->data = [];
    $contents->new = (object) array();

$book = (object) array();

    $book->target = "";

    $book->author = "";

    $book->title = "";
    $book->description = "";
    $book->tags = "";

    $book->preface = "";
    $book->notes = [];
    $book->note = $note;

    $book->contents = [];

    $book->glossary = $glossary;
    $book->definitions = [];
    $book->definition = $definition;

    $book->isbn = "";
    $book->publisher = "";

$author = (object) array();

    $author->name = "";
    $author->details = (object) array();

        $author->details->images = $images;
        $author->details->content = $contents;

    $author->contact = (object) array();

        $author->contact->phone = "";
        $author->contact->email = "";
        $author->contact->address = "";
        $author->contact->chat = "";

$authors = (object) array();

    $authors->signed = [];
    $authors->involved = [];
    $authors->listed = [];

    $authors->new = $author;

$subscriber = (object) array();

    $subscriber->name = "";
    $subscriber->contact = "";
    $subscriber->start = "";
    $subscriber->lapses = 0;

$subscribers = (object) array();

    $subscribers->enrolled = [];
    $subscribers->expiring = [];

    $subscribers->new = $subscriber;

$subscription = (object) array();

    $subscription->html = $html;

    $subscription->target = "";

    $subscription->title = "";
    $subscription->description = "";
    $subscription->subscribers = $subscribers;
    $subscription->content = [];

/* file systems and api */
$api = (object) array();

    $api->html = $html;
    
    $api->target = "";

    $api->url = "";
    $api->hook = "";

    $api->secret_code = null;
    $api->token = null;

$filesys = (object) array();

    $filesys->html = $html;

    $filesys->base = "";
    $filesys->root = "";

    $filesys->heirarchy = (object) array();
    
        $filesys->heirarchy->folders = [];
        $filesys->heirarchy->files = [];
    
    $filesys->folders = [];
    $filesys->folder = $folder;

    $filesys->files = [];
    $filesys->files = $file;

    $filesys->uploads = [];
    $filesys->upload = (object) array();
    $filesys->downloads = [];
    $filesys->download = (object) array();

    $filesys->images = [];
    $filesys->image = $image;
    $filesys->documents = [];
    $filesys->document = $rtf;
    $filesys->packages = [];
    $filesys->package = $package;

    $filesys->privacy = "";

    $filesys->tmp = (object) array();

        $filesys->tmp->base = "";
        $filesys->tmp->folders = [];
        $filesys->tmp->files = [];

/* calendar and events */

$event = (object) array();

    $event->html = $html;

    $event->author = "";
    $event->cal = "";
    $event->parent = "";

    $event->created = "";
    $event->tags = "";
    $event->privacy = "";
    $event->security = $security;
    $event->access = $access;

    $event->invoked = "";
    $event->persists = "";
    $event->expires = "";
    $event->status = "";

    $event->type = "";
    $event->title = "";
    $event->description = "";
    $event->details = "";

    $event->recurring = false;
    $event->location = false;
    $event->tradition = "";

$events = (object) array();

    $events->target = "";
    $events->calendars = [];
    $events->new = $event;

$today = (object) array();

    $today->data = new DateTime;

    $today->date = $day->data->format("");
    $today->time = $day->data->format("");
    $today->datetime = $day->data->format("");
    
    $today->number = $day->data->format("");
    $today->num = $day->data->format("");
    
    $today->dotw = $day->data->format("");
    $today->sdotw = $day->data->format("");
    $today->woty = $day->data->format("");

    $today->month = $day->data->format("");
    $today->smonth = $day->data->format("");
    $today->dotm = $day->data->format("");
    $today->sdotm = $day->data->format("");
    $today->nmonth = $day->data->format("");
    $today->snmonth = $day->data->format("");
    $today->moty = $day->data->format("");
    $today->nmoty = $day->data->format("");

    $today->year = $day->data->format("");
    $today->syear = $day->data->format("");
    $today->doty = $day->data->format("");

    $today->season = "";

    $today->tomorrow = (object) array();
    $today->yesterday = (object) array();

    $today->next = (object) array();
    $today->previous = (object) array();

    $today->events = [];
    $today->event = $event;

    $today->next_events = [];
    $today->next_event = $event;
    $today->previous_events = [];
    $today->previous_event = $event;
    $today->first_events = [];
    $today->first_event = $event;

$week = (object) array();
$month = (object) array();
$season = (object) array();
$year = (object) array();

$calendar = (object) array();

    $calendar->html = $html;

    $calendar->settings = (object) array();

        $calendar->settings->general = (object) array();

        $calendar->settings->view = (object) array();

            $calendar->settings->view->daily = (object) array();
            $calendar->settings->view->weekly = (object) array();
            $calendar->settings->view->monthly = (object) array();
            $calendar->settings->view->seasonal = (object) array();
            $calendar->settings->view->yearly = (object) array();

        $calendar->settings->clocks = (object) array();

            $calendar->settings->clocks->general = (object) array();
            $calendar->settings->clocks->alarms = (object) array();
            $calendar->settings->clocks->timers = (object) array();
            $calendar->settings->clocks->timestamps = (object) array();

    $calendar->target = "";
    $calendar->privacy = "";

    $calendar->tags = "";
    $calendar->book = "";

    $calendar->timezone = "";
    $calendar->today = $today;

    $calendar->map = $book;
    
        $calendar->map->date = new DateTime;
        $calendar->map->today = $calendar->model->date->format("Y-m-d");
        $calendar->map->tomorrow = (object) array();
        $calendar->map->yesterday = (object) array();

        $calendar->map->events = [];
        $calendar->map->event = $event;
        
            $calendar->map->event->invites = [];
            $calendar->map->event->rsvp = [];

$clock = (object) array();

    $clock->target = "";
    $clock->tags = "";
    $clock->book = "";
    
    $clock->date = new DateTime;
    $clock->today = $clock->date->format("Y-m-d");
    $clock->time = $clock->date->format("H:i:s");
    $clock->events = [];

    $clock->privacy = $privacy;
    $clock->security = $security;
    $clock->grant = "";

    $clock->forms = $forms;
    $clock->event = $event;

$schedule = $calendar;

    $schedule->title = "";
    $schedule->description = "";

$agenda = (object) array();

    $agenda->target = "";

    $agenda->schedules = [];
    $agenda->new = $schedule;

/* rolodex */

$contact = (object) array();

    $contact->tags = "";
    $contact->book = "";
    $contact->event = "";

    $contact->name = "";
    $contact->birthday = "";
    $contact->death = "";
    $contact->address = "";
    $contact->phone = "";
    $contact->email = "";

    $contact->bios = "";
    $contact->notes = [];
    $contact->note = $note;

    $contact->first = "";
    $contact->recent = "";
    $contact->previous = "";
    $contact->next = "";

    $contact->privacy = $privacy;
    $contact->security = $security;
    $contact->grant = "";

    $contact->forms = $forms;

$address = (object) array();

    $address->book = "";
    $address->text = "";

    $address->physical = (object) array();

        $address->physical->street = "";
        $address->physical->city = "";
        $address->physical->state = "";
        $address->physical->zip = "";
    
    $address->site = (object) array();

        $address->site->url = "";

$rolodex = (object) array();

    $rolodex->html = $html;
    $rolodex->entries = [];

    $rolodex->address = $address;
    
        $rolodex->address->book = [];
        $rolodex->address->new = $address;

/* mailbox, mail */

$mail = (object) array();

    $mail->tags = "";
    $mail->book = "";
    $mail->folder = "";

    $mail->sender = "";
    $mail->recipient = "";

    $mail->subject = "";
    $mail->body = "";
    $mail->attachments = "";
    $mail->cost = "";

    $mail->priority = "";
    $mail->privacy = "";

    $mail->forms = [];

$mailbox = (object) array();

        $mailbox->settings->general = (object) array();
        $mailbox->settings->inbox = (object) array();
        $mailbox->settings->outbox = (object) array();
        $mailbox->settings->folders = (object) array();
        $mailbox->settings->contacts = (object) array();
        $mailbox->settings->calendar = (object) array();

    $mailbox->html = $html;

    $mailbox->inbox = (object) array();
    
        $mailbox->inbox->title = "Inbox";
        $mailbox->inbox->mail = [];

        $mailbox->inbox->unread = 0;
        $mailbox->inbox->read = 0;
        $mailbox->inbox->max = false;

    $mailbox->outbox = (object) array();

        $mailbox->outbox->title = "Outbox";
        $mailbox->outbox->mail = [];

        $mailbox->outbox->no_reply = 0;
        $mailbox->outbox->replied = 0;
        $mailbox->outbox->max = false;
    
    $mailbox->flagged = (object) array();

        $mailbox->flagged->title = "Flagged";

        $mailbox->flagged->general = [];
        $mailbox->flagged->spam = [];

        $mailbox->flagged->good = [];
        $mailbox->flagged->bad = [];

    $mailbox->folders = [];

$mailbox->folder = $folder;

$mailbox->rolodex = $rolodex;
$mailbox->calendar = $calendar;

$mailbox->message = $mail;

/* helpdesk: legal docs, guides, faqs, tickets */
$legal = (object) array();

    $legal->conditions = "";
    $legal->services = "";
    $legal->privacy = "";

    $legal->practices = [];
    $legal->exercises = [];

    $legal->records = [];

$legal->practice = $article;
$legal->exercise = $article;
$legal->record = $article;

$helpdesk = (object) array();

    $helpdesk->forms = [];

    $helpdesk->docs = (object) array();

    $helpdesk->guides = (object) array();
    $helpdesk->faq = (object) array();

    $helpdesk->tickets = [];

$helpdesk->doc = (object) array();

    $helpdesk->doc->forms = [];

    $helpdesk->doc->tags = "";
    $helpdesk->doc->parent = "";

    $helpdesk->doc->op = "";

    $helpdesk->doc->title = "";
    $helpdesk->doc->body = "";
    $helpdesk->doc->attachments = [];

    $helpdesk->doc->item = (object) array();

$helpdesk->guide = (object) array();
$helpdesk->faq->item = (object) array();

$helpdesk->ticket = (object) array();

    $helpdesk->ticket->forms = [];

    $helpdesk->ticket->category = "";
    $helpdesk->ticket->type = "";
    $helpdesk->ticket->priority = "";

    $helpdesk->ticket->op = "";
    $helpdesk->ticket->message = "";
    $helpdesk->ticket->status = "";
    $helpdesk->ticket->resolved = false;

    $helpdesk->ticket->created = "";
    $helpdesk->ticket->stamps = "";
    $helpdesk->ticket->privacy = "";

/* journal: notebooks, notes, lists, tasks, albums, dates, addresses, schedules, ledgers */

$ledger = (object) array();

    $ledger->book = "";
    $ledger->target = "";

    $ledger->title = "";
    $ledger->description = "";
    $ledger->tags = "";

    $ledger->items = [];

    $ledger->item = (object) array();

        $ledger->item->target = "";

        $ledger->item->title = "";
        $ledger->item->description = "";

        $ledger->item->type = "";
        $ledger->item->value = "";

        $ledger->item->lowest = "";
        $ledger->item->low = "";
        $ledger->item->high = "";
        $ledger->item->highest = "";

$ledgers = (object) array();

    $ledgers->books = [];

    $ledgers->new = $ledger;

$journal = (object) array();

    $journal->settings = (object) array();

        $journal->settings->general = (object) array();
        $journal->settings->note = (object) array();
        $journal->settings->list = (object) array();
        $journal->settings->album = (object) array();
        $journal->settings->address = (object) array();
        $journal->settings->scheduling = (object) array();
        $journal->settings->ledger = (object) array();
        $journal->settings->event = (object) array();
    
    $journal->notebooks = $books;
    $journal->notes = $notes;
    $journal->lists = $lists;
    $journal->projects = $projects;
    $journal->tasks = $tasks;
    $journal->albums = $albums;
    $journal->events = $events;
    $journal->addresses = $addresses;
    $journal->schedules = $schedules;
    $journal->ledgers = $ledgers;

$journal->book = (object) array();

    $journal->book->target = "";
    $journal->book->title = "";
    $journal->book->description = "";

    $journal->book->op = "";

    $journal->book->created = "";
    
    $journal->book->privacy = "";

    $journal->book->forms = [];

$journal->note = $note;

$journal->list = $list;

$journal->task = $list->item;

    $journal->task->target = "";
    $journal->task->tags = "";
    $journal->task->notebook = "";
    $jouranl->task->clock = "";
    $jouranl->task->schedule = "";

    $journal->task->sender = "";
    $journal->task->recipient = "";

    $journal->task->activity = "";
    $journal->task->references = "";
    $journal->task->attachements = "";

$jounral->task->item = (object) array();

    $task->item->target = "";
    $task->item->tags = "";
    $task->item->notebook = "";

    $task->item->op = "";

    $task->item->sender = "";
    $task->item->recipient = "";

    $task->item->title = "";
    $task->item->status = "";
    $task->item->description = "";
    $task->item->references = "";

    $task->item->privacy = "";
    $task->item->stamps = "";

$journal->album = (object) array();

    $journal->album->target = "";
    $journal->album->tags = "";
    $journal->album->notebook = "";

    $journal->album->op = "";

    $journal->album->title = "";
    $journal->album->type = "";
    $journal->album->items = [];

    $journal->album->privacy = "";
    $journal->album->stamps = "";

$journal->album->item = $item;

$journal->schedule = (object) array();

    $journal->schedule->target = "";
    $journal->schedule->tags = "";
    $journal->schedule->notebook = "";

    $journal->schedule->title = "";
    $journal->schedule->description = "";
    $journal->schedule->details = "";
    $journal->schedule->events = [];

    $journal->schedule->privacy = "";
    $journal->schedule->stamps = "";

$journal->schedule->event = $event;

$journal->ledger = (object) array();

    $journal->ledger->target = "";
    $journal->ledger->tags = "";
    $journal->ledger->notebook = "";

    $journal->ledger->title = "";
    $journal->ledger->description = "";
    $journal->ledger->reports = [];
    $journal->ledger->items = [];

    $journal->ledger->privacy = "";
    $journal->ledger->stamps = "";

$journal->ledger = (object) array();
$journal->ledger->item = (object) array();

    $journal->ledger->item->ledger_id = "";

    $journal->ledger->item->text = "";

    $journal->ledger->item->min = "";
    $journal->ledger->item->low = "";
    $journal->ledger->item->under = "";
    $journal->ledger->item->even = "";
    $journal->ledger->item->over = "";
    $journal->ledger->item->high = "";
    $journal->ledger->item->max = "";

/* site: map, seo, layout, assets, libraries, api, apis,  */
$site = (object) array();

    $site->map = (object) array();

        $site->map->core = [];
        $site->map->platform = [];
        $site->map->global = [];
        $site->map->local = [];
        /* $site->map->universal = [];
        $site->map->multi = [];
        $site->map->radial = [];
        $site->map->resial = [];
        $site->map->divinial = [];
        $site->map->collapsed = []; */
    
    $site->seo = (object) array();
    
        $site->seo->doctype = "<!DOCTYPE html>";
        $site->seo->lang = "en";

        $site->seo->title = "";
        $site->seo->description = "";
        $site->seo->keywords = "";
        $site->seo->charset = "";

        $site->seo->robots = [];
        $site->seo->analytics = null;
        $site->seo->ads = null;
    
    $site->layout = (object) array();

        $site->layout->meta = [];
        $site->layout->css = [];
        $site->layout->js = [];
        $site->layout->apis = [];

        $site->layout->navigation = [];
        $site->layout->panels = [];

        $site->layout->grid = $grid;

    $site->assets = (object) array();
    
        $site->assets->bootstrap = null;
        $site->assets->font_awesome = null;
        $site->assets->jquery = null;
        $site->assets->google_fonts = null;
        $site->assets->framework = null;

    $site->libraries = (object) array();

        $site->libraries->framework = false;
        $site->libraries->highlight_code = null;
        $site->libraries->code_editor = null;
        $site->libraries->text_editor = null;
        $site->libraries->html_editor = null;
        $site->libraries->php_editor = null;
        $site->libraries->js_editor = null;
        $site->libraries->sql_editor = null;
        $site->libraries->db_manager = null;
        $site->libraries->file_manager = null;
        $site->libraries->upload_manager = null;
        $site->libraries->download_manager = null;

    $site->api = (object) array();
    
        $site->api->target = "";
        $site->api->file = "";

        $site->api->docs = "";

    $site->apis = (object) array();

        $site->apis->internal = null;
        $site->apis->local = null;

        $site->apis->facebook = null;
        $site->apis->twitter = null;
        $site->apis->google = null;
        $site->apis->youtube = null;
        $site->apis->github = null;

        $site->apis->other = [];
    
    $site->legal = $legal;

/* page: map, links, breadcrumb, layout, group, profile, target, path, route, folder, file, include, content */
$site->page = false;

$page = (object) array();

    // all pages
    $page->map = [];
    $page->links = [];
    $page->breadcrumb = [];
    $page->layout = [];
    
    // active page
    // preloaded
    $page->group = null;
    $page->profile = null;
    $page->target = page_segment(count_page_segments());
    $page->path = page_segment((count_page_segments() - 1), true);
    $page->route = page_segment(true);

    // instantiated
    $page->folder = null;
    $page->file = null;
    $page->include = null;
    $page->content = null;

/* users, user: id, expected_id, assumed_id, proven_id, settings, cards: profile, general, (employee, (developer), mailbox, journal */
$grant = (object) array();

    $grant->level = 0;
    $grant->access = true;

    $grant->description = "";

$clearance = (object) array();

    $clearance->foreign = $grant;
    $clearance->none = $grant;
    $clearance->restricted = $grant;
    $clearance->confidential = $grant;
    $clearance->secret = $grant;
    $clearance->top_secret = $grant; 
    $clearance->black_ops = $grant;
    $clearance->master_ops = $grant;

$clearances = (object) array();

    $clearances->registered = [];
    $clearances->new = $clearance;

$developer = (object) array();

    $developer->code_word = "";
    $developer->reply_word = "";

    $developer->enrollment = (object) array();

        $developer->enrollment->status = "";
        $developer->enrollment->security = "";
        $developer->enrollment->clearance = "";
    
    $developer->clearances = $clearances;

$user = (object) array();

    $user->profile = (object) array();

        $user->profile->type = get_user_type();
        $user->profile->ip_address = get_user_ip();
        $user->profile->browser = get_user_browser();
        $user->profile->timezone = get_user_timezone();
        $user->profile->role = "Guest";

        $user->profile->status = (object) array();

            $user->profile->status->code = generate_code();
            $user->profile->status->token = encode_token($user->profile->status->code);
            $user->profile->status->resolved = false;
        
        $user->profile->security = (object) array();

            $user->profile->security->expiration = "3 days";
            $user->profile->security->auth = null; # Session | Form
            $user->profile->security->method = null; # Full Auto | Semi Auto | Manual
            $user->profile->security->grant = null; # Criticial | Non-Critical

$user->card = (object) array();

    $user->card->profile = $user->profile;

    $user->card->roles = $roles;

    $user->card->access = $access;

    $user->card->general = (object) array();

        $user->card->general->name = "";
        $user->card->general->email = "";
        $user->card->general->phone = "";
        $user->card->general->address = "";
    
    $user->card->developer = $developer;

$user->settings = (object) array();

        $user->settings->general = (object) array();
            $user->settings->general->offline_enabled = false;
            $user->settings->general->cookie_policy = true;

        $user->settings->security = (object) array();
            $user->settings->security->login_policy = false;
        
        $user->settings->calendar = $calendar->settings;
        $user->settings->mailbox = $mailbox->settings;
        $user->settings->journal = $journal->settings;

        $user->settings->card = (object) array();

$users = (object) array();

    $users->settings = (object) array();

        $users->settings->general = (object) array();

            $users->settings->general->open_registration = true;
            $users->settings->general->user_uploads_enabled = true;
            $users->settings->general->user_filesys_enabled = true;

            $users->settings->general->user_calendar_enabled = true;
            $users->settings->general->user_mailbox_enabled = true;
            $users->settings->general->user_journal_enabled = true;

    $users->registered = [];
    $users->new = $user;

    $users->forms = $forms;

$resume = (object) array();

    $resume->title = "";

    $resume->user = "";
    $resume->skills = (object) array();
    
        $resume->skills->general = [];
        $resume->skills->advanced = [];

    $resume->experience = (object) array();
    
        $resume->experience->unpaid = [];
        $resume->experience->paid = [];
    
    $resume->education = (object) array();

        $resume->school = "";
        $resume->certification = "";
        $resume->training = "";

$employee = $user;

    $employee->dates = $agenda->new;

    $employee->dates->hired = $schedule;
    $employee->dates->orientation = $schedule;
    $employee->dates->training = $schedules;
    $employee->dates->last_day = false;

    $employee->agenda = $agenda->new;

$coworker = $employee;

$coworkers = (object) array();

    $coworkers->available = [];
    $coworkers->assigned = [];

    $coworkers->new = $coworker;

$biz = (object) array();

    $biz->title = "";
    $biz->type = "";
    $biz->description = "";
    $biz->slogan = "";

    $biz->legal = $legal;

    $biz->employees = [];
    $biz->customers = [];

    $biz->customer = (object) array();

        $biz->customer->name = "";
        $biz->customer->purchases = "";

    $biz->market = (object) array();
    
        $biz->market->pos = [];
        $biz->market->stalls = [];
    
    $biz->pos = (object) array();

        $biz->pos->target = "";
        
        $biz->pos->employees = [];

        $biz->pos->sales = [];
        $biz->pos->returns = [];
        $biz->pos->cancels = [];

        $biz->pos->credits = [];

        $biz->pos->profit = "";
    
    $biz->market->stall = (object) array();

        $biz->market->stall->target = "";
        $biz->market->stall->tags = "";

        $biz->market->stall->pos = $biz->pos;

        $biz->market->stall->title = "";
        $biz->market->stall->description = "";
        $biz->market->stall->products = [];
    
    $biz->products = [];

    $biz->product = (object) array();
    
        $biz->product->title = "";
        $biz->product->supplier = "";
        $biz->product->description = "";
        $biz->product->specifics = "";

        $biz->product->details = "";
        $biz->product->audits = [];

        $biz->product->audit = (object) array();

            $biz->product->audit->id = "";
            $biz->product->audit->employee = "";

            $biz->product->audit->received = "";
            $biz->product->audit->available = "";
            $biz->product->audit->sold = "";

            $biz->product->audit->incoming = (object) array();
            $biz->product->audit->outgoing = (object) array();

        $biz->product->sales = [];

        $biz->product->sale = (object) array();

            $biz->product->sale->product = "";
            $biz->product->sale->consumers = "";

            $biz->product->sale->advertising = "";

        $biz->product->expenses = [];
        $biz->product->expense = (object) array();

            $biz->product->expense->title = "";
            $biz->product->expense->description = "";

            $biz->product->expense->net = (object) array();

                $biz->product->expense->net->ytd = "";
                $biz->product->expense->net->mtd = "";
                $biz->product->expense->net->dtd = "";

                $biz->product->expense->net->low = "";
                $biz->product->expense->net->high = "";
            
            $biz->product->expense->title = "";

        $biz->product->profits = [];
        $biz->product->profit = (object) array();

            $biz->product->profit->title = "";
            $biz->product->profit->description = "";

            $biz->product->profit->net = (object) array();

                $biz->product->profit->net->ytd = "";
                $biz->product->profit->net->mtd = "";
                $biz->product->profit->net->dtd = "";

                $biz->product->profit->low = "";
                $biz->product->profit->high = "";

        $biz->product->tags = "";
        $biz->product->stalls = "";

    $biz->receipts = [];

    $biz->receipt = (object) array();

        $biz->receipt->customer = "";
        $biz->receipt->products = [];

        $biz->receipt->net = (object) array();

            $biz->receipt->net->grand = "";

            $biz->receipt->net->discounts = "";
            $biz->receipt->net->coupons = "";

            $biz->receipt->net->subtotal = "";

            $biz->receipt->net->taxes = "";
            $biz->receipt->net->total = "";

        $biz->receipt->message = "";
        $biz->receipt->reward = false;

$biz->employee = (object) array();

?>
