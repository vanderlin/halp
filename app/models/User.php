<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends BaseModel implements ConfideUserInterface {
    
    use ConfideUser;
    use HasRole; 
    protected $hidden = array('password', 'remember_token', 'confirmation_code', 'confirmed', 'google_token', 'set_password', 'notifications');

    public function roles() {
        return $this->belongsToMany(Config::get('entrust::role'), Config::get('entrust::assigned_roles_table'), 'user_id', 'role_id')->withTimestamps();
    }

    public function toArray() {
        $array = parent::toArray();
        $array['name'] = $this->getName();
        if($this->profileImage()->first()) {
            $array['profile_image'] = URL::to($this->profileImage->url());
            $array['profile_image_base'] = URL::to($this->profileImage->resizeImageURL());
        }
        $array['roles'] = $this->roles()->lists('name');
        $array['url'] = $this->getProfileURL();
        return $array;
    }

    public static function missingProfileImage() {

        $m = Asset::findFromTag('missing-user-image');
        
        if($m == NULL) {
            return Asset::missingFile();   
        }
        return $m;
    }

    // ------------------------------------------------------------------------
    public function scopeOrderByCreatedTasks($query)
    {
        /*
        SELECT 
        users.id,
        users.firstname,
        count(tasks.id) AS total_created
        FROM users
        LEFT JOIN tasks ON tasks.creator_id = users.id
        GROUP BY users.id
        */
        return $query->select(array('users.*', DB::raw("COUNT(tasks.id) as total_created_tasks")))
                     ->join('tasks', function($join) {
                        $join->on('tasks.creator_id', '=', 'users.id');
                     })  
                     ->groupBy("users.id")
                     ->orderBy("total_created_tasks", 'DESC')
                     ->orderBy("tasks.created_at", 'DESC');

    }

    // ------------------------------------------------------------------------
    public function scopeOrderByClaimedTask($query)
    {
        /*
        SELECT COUNT(tasks.id) as claimed_tasks, users.id, users.username, tasks.id as task_id FROM users 
        LEFT JOIN tasks
        ON tasks.claimed_id = users.id AND tasks.deleted_at IS NULL
        GROUP BY users.id
        ORDER BY claimed_tasks DESC, tasks.claimed_at DESC
        */;

        return $query->select(array("users.*", 
                    DB::raw("COUNT(tasks.id) as total_claimed_tasks")))
                     ->leftJoin('tasks', function($join) {
                        $join->on('tasks.claimed_id', '=', 'users.id')->whereNull('tasks.deleted_at');
                     })
                     ->groupBy("users.id")
                     ->orderBy("total_claimed_tasks", 'DESC')
                     ->orderBy("tasks.claimed_at", 'DESC');
    }

    // ------------------------------------------------------------------------
    /*public function scopeMostHelpfulForWeek($query, $week_of=null)
    {
        if($week_of == null) $week_of = Carbon\Carbon::now();
        $date_str = $week_of->toDateString();

        
        // -- Most helpful this week --
        // SELECT 
        //     users.*, 
        //     YEARWEEK(tasks.`created_at`) AS task_date, 
        //     YEARWEEK(CURDATE()) AS cur_date,
        //     DATE_FORMAT(tasks.`created_at`, '%W') AS task_date_name,
        //     COUNT(tasks.`id`) AS total_claimed_this_week
            
        // FROM `users` 
        // LEFT JOIN tasks
        // ON tasks.`claimed_id` = users.`id` AND tasks.`deleted_at` IS NULL AND YEARWEEK(tasks.`created_at`) = YEARWEEK(CURDATE())
        // GROUP BY users.`id`
        // ORDER BY total_claimed_this_week DESC, tasks.claimed_at DESC
        

        return $query->select(array(
                        "users.*", 
                        DB::raw("COUNT(tasks.id) as total_claimed_this_week")))
                        ->leftJoin('tasks', function($join) use($date_str) {
                            $join->on('tasks.claimed_id', '=', 'users.id')
                                 ->on(DB::raw("YEARWEEK(tasks.`created_at`) = YEARWEEK('{$date_str}')"), DB::raw(''), DB::raw(''))
                                 ->whereNull('tasks.deleted_at');
                        })
                     ->groupBy('users.id')
                     ->orderBy('total_claimed_this_week', 'DESC')
                     ->orderBy('tasks.claimed_at', 'DESC');
    }*/

    // ------------------------------------------------------------------------
    public function scopeMostHelpfulForProject($query)
    {
        /*
        -- Most helpful for project --
        SELECT 
            users.id, users.firstname, 
            tasks.`created_at` AS task_date, 
            COUNT(tasks.`id`) AS total_claimed_for_project,
            tasks.project_id,
            projects.title AS project_name
            
        FROM `users` 

        LEFT JOIN tasks ON tasks.`claimed_id` = users.`id` AND tasks.`deleted_at` IS NULL 

        RIGHT JOIN projects ON tasks.`project_id` = projects.`id`
         
        GROUP BY tasks.project_id
        ORDER BY total_claimed_for_project DESC, tasks.claimed_at DESC
        

        return $query->select(array(
                        "users.*", 
                        "tasks.project_id as most_helped_project",
                        DB::raw("COUNT(tasks.id) as total_claimed_for_project")))
                        ->leftJoin('tasks', function($join) {
                            $join->on('tasks.claimed_id', '=', 'users.id')
                                 ->whereNull('tasks.deleted_at');
                        })
                        ->leftJoin('projects', function($join) {
                            $join->on('tasks.project_id', '=', 'projects.id');
                        })
                     ->groupBy('tasks.project_id')
                     ->orderBy('total_claimed_for_project', 'DESC')
                     ->orderBy('tasks.claimed_at', 'DESC');
    }

    // ------------------------------------------------------------------------
    public function claimedTasksForWeek($week_of=null)
    {
        if($week_of == null) $week_of = Carbon\Carbon::now();
        $date_str = $week_of->toDateString();
        return $this->claimedTasks();
    }
    // ------------------------------------------------------------------------
    public function scopeAdmin($query) {   
       return $query->whereHas('roles', function($q) { $q->where('roles.name', '=', 'Admin'); });
    }

    // ------------------------------------------------------------------------
    public function userable() {
      return $this->morphTo();
    }

    // ------------------------------------------------------------------------
    public static function findFromData($data) {
        return \User::where('id', '=', $data)->orWhere('username', '=', $data)->orWhere('email', '=', $data)->first();
    }

    public static function isMe($user) {
        return Auth::check() && ($user->id == Auth::user()->id || Auth::user()->HasRole('Admin'));
    }

    public static function isNotMine($user) {
        return Auth::check() && ($user->id != Auth::user()->id);
    }
    
    public function profileImage() {
        return $this->morphOne('Asset', 'assetable');
    }

    public function getTotalCompletedTask()
    {
        return 10;
    }

    public function totalClaimed()
    {
        return $this->hasMany('Task\Task', 'claimed_id')->count();
    }

    public function totalCreated()
    {
        return $this->hasMany('Task\Task', 'creator_id')->count();
    }
    

    public function claimedTasks()
    {
        return $this->hasMany('Task\Task', 'claimed_id');
    }

    public function getTaskRatioAttribute() {
        $created = $this->totalCreated();
        $claimed = $this->totalClaimed();
        if($claimed == 0 && $created == 0) return 0;
        return $claimed / ($claimed+$created);
    }

    public function createdTasks()
    {
        return $this->hasMany('Task\Task', 'creator_id');
    }

    public function notificationEvents()
    {
        return $this->hasMany('Notification', 'object_id')->where('object_type', 'User');
    }

    public function getProfileImageAttribute() {
        $img = $this->profileImage()->first();

        if($img == null) {
            return User::missingProfileImage();
        }
        return $img;
    }

    public function makeDefaultProfileImage() {
        $userImage = $this->profileImage()->first();
        if($userImage == null) {
            $userImage = new Asset;
        }
        $userImage->path = 'assets/content/users';
        $userImage->saveRemoteImage('assets/content/common/porfile-default.png', 
                                    $this->username.'_'.$this->id.'.png');
        $userImage->user()->associate($this);
        $userImage->save();
        $this->profileImage()->save($userImage);
    }

    public function hasDefaultProfileImage() {
       return $this->profileImage()->first() == null;
    }

    public function getName() {
        return (empty($this->firstname)||empty($this->lastname)) ? $this->username : ucfirst($this->firstname)." ".ucfirst($this->lastname);
    }
    public function getShortName()
    {
        return (empty($this->firstname)||empty($this->lastname)) ? $this->username : ucfirst($this->firstname).' '.strtoupper(substr($this->lastname, 0, 1)).'.';
    }
    public function getFirstName() {
        return empty($this->firstname) ? $this->username : $this->firstname;
    }

    public function getRoleName() 
    {
        if($this->hasRole('Admin')) return 'Admin';
        if($this->hasRole('Editor')) return 'Editor';
        return $this->isSpotter() ? 'Spotter' : 'Local';
    }
    
    public function isAdmin() 
    {
        return $this->hasRole('Admin');
    }

    public function isEditor() 
    {
        return ($this->hasRole('Admin') || $this->hasRole('Editor'));
    }

    public function isSpotter() 
    {
        return $this->hasRole('Writer');
    }

    public function getRoles() 
    {
        return implode(", ", $this->roles()->lists('display_name'));
    }

    public function hasToken() {
        return isset($this->google_token);
    }

    public function getToken() 
    {
        return $this->google_token;
    }

    public function updateFromGoogleAccount() 
    {
        $client = GoogleSessionController::getClient();
        $oauth2 = new \Google_Auth_OAuth2($client);


        if($this->google_token && $oauth2->isAccessTokenExpired() == false) {

            $oauth2->refreshToken($this->google_token);
            $client->setAccessToken($oauth2->getAccessToken());

            $oauth2 = new \Google_Service_Oauth2($client);
            $google_user = $oauth2->userinfo->get();

            // update the photo
            GoogleSessionController::saveGoogleProfileImage($google_user, $this);
            
            // other things later..

            return true;
        }
            
        return false;

    }


    public function getProfileLinkAttribute() 
    {
        return URL::to('users/'.$this->username);
    }

    public function getProfileURL() 
    {
        return URL::to('users/'.$this->username);
    }

     public function getURL($relative = true) 
     {
        return $relative ? '/users/'.$this->username : URL::to('users/'.$this->username);
    }

    public function getEditProfileURL() 
    {
        return URL::to('admin/profile');
    }

    public function getEditURL() 
    {
        return URL::to('admin/users/'.$this->id.'/edit');
    }

    static function findFromEmail($email) {
        $user = User::where('email', '=', $email)->first();   
        return $user;
    }

    // ------------------------------------------------------------------------
    public function delete() {
        
        
        if($this->hasDefaultProfileImage()==false) {
            $this->profileImage->delete();
        }
        foreach ($this->createdTasks as $task) {
            $task->delete();
        }
        foreach ($this->notificationEvents as $events) {
            $events->delete();
        }
        
        

        parent::delete();
    }
}