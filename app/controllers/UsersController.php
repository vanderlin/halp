<?php

use Task\Task;

/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends BaseController
{

    /**
     * Displays the form for account creation
     *
     * @return  Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('site.user.register');
    }

    public function register() 
    {
        return Redirect::to('login');
    }

    public function index()
    {
        
        
        /*
        $users_q = User::join('tasks', 'tasks.claimed_id', '=', 'users.id')
                    ->groupBy('users.id')
                    ->orderBy('total_claimed_tasks', 'DESC')
                    ->get(['users.*', DB::raw("count(".DB::getTablePrefix()."tasks.id) as total_claimed_tasks")]);
        
        // Paginate users
        $perPage = 10;
        $currentPage = Input::get('page') - 1;
        $pagedData = $users_q->slice($currentPage * $perPage, $perPage)->all();
        $users = Paginator::make($pagedData, count($users_q), $perPage);
        */

        $users = User::orderByClaimedTask()->get();
        $leader = User::orderByClaimedTask()->first();
        $top_user_last_week = User::mostHelpfulForWeek(Carbon\Carbon::now()->subWeek())->first();
        $top_active_project = Project::orderByMostTasks()->with('user')->first();
        $top_user_created_tasks = User::orderByCreatedTasks()->first();
        
        $data = [
            'top_user_last_week'     => $top_user_last_week,
            'top_active_project'     => $top_active_project,
            'top_user_created_tasks' => $top_user_created_tasks,
            'users'                  => $users, 
            'leader'                 => $leader
            ];

        return View::make('site.user.index', $data);
    }

    // ------------------------------------------------------------------------
    public function unsubscribe()
    {
        $user = Auth::user();
        $user->notifications = false;
        $user->save();
        return Redirect::to($user->getProfileURL());
    }

    // ------------------------------------------------------------------------
    public function show($username)
    {   
        $user = User::findFromData($username);
        return View::make('site.user.show', ['user'=>$user]);
    }

    // ------------------------------------------------------------------------
    public function update($username)
    {   
        $message = "";
        $user = \User::findFromData($username);
        if($user === null) return $this->statusResponse(['errors'=>'No User found']);

        if(Input::has('notifications') && Input::get('notifications')!== $user->notification)
        {
            $user->notifications = Input::get('notifications');
            $user->save();
        }
        
        if(Input::has('password') && Input::has('password_confirmation'))
        {
            $user->password = Input::get('password');
            $user->password_confirmation = Input::get('password_confirmation');
            $user->set_password = true;
            if($user->save() == false) 
            {
                return $this->statusResponse(['error'=>$user->errors()->all()]);
            }
            $message .= "Password Updated";
        }

        return $this->statusResponse(['notice'=>$message, 'user'=>$user]);;
    }

    // ------------------------------------------------------------------------
    public function editUserRoles($id) {

        $user = User::findOrFail($id);
        if($user) {

            $current_roles = $user->roles;
            $spotter_role = Role::whereName('Writer')->first();
            $has_spotter_role = false;
            $rolesToAttach = [];
            foreach (Input::get('roles') as $key => $value) {
                $role = Role::where('id', '=', $key)->first();
                if($key == $spotter_role->id) {
                    $has_spotter_role = true;
                }
                if($role) {
                    array_push($rolesToAttach, $key);
                }
            }

            if(count($rolesToAttach) > 0) {
                $user->roles()->sync($rolesToAttach);
            }

            
            $had_spotter_role_already = false;
            foreach ($current_roles as $role) {
                if($role->id == $spotter_role->id) {
                    $had_spotter_role_already = true;
                }
            }
            if($has_spotter_role && $had_spotter_role_already == false) {
                // Activity::createActivityAndFire([
                //     'name'=>Activity::ACTIVITY_USER_SPOTTER, 
                //     'timestamp'=>Carbon\Carbon::now(), 
                //     'parent'=>$user, 
                //     'user_id'=>$user->id
                // ]);    
            }
            


            return Redirect::back()->with('notice', 'User updated');
        }

        return Redirect::back()->with('error', 'Could not find user');
    }

    // ------------------------------------------------------------------------
    public function updateFromGoogle($id) {
        $user = User::find($id);        
        if($user) {
            if($user->updateFromGoogleAccount() === false) {
                return Redirect::to(GoogleSessionController::generateGoogleLoginURL(['approval_promt'=>'force', 'state'=>'updated_profile']));
            } 
        }
        return Redirect::back()->with(['status'=>'Profile updated from google']);

    }
    // ------------------------------------------------------------------------
    public function updateProfile($id) {


        $user = User::find($id);
        if($user === null) return $this->statusResponse(['errors'=>'No User found']);

        // password
        if( Input::has('password') && Input::has('password_confirmation')) {
            $input = ['password'=>Input::get('password'),
                      'password_confirmation'=>Input::get('password_confirmation')];

            $user->password = $input['password'];
            $user->password_confirmation = $input['password_confirmation'];                        
        }


        if(Input::has('firstname')) {
            $user->firstname = Input::get('firstname');
        }
        if(Input::has('lastname')) {
            $user->lastname = Input::get('lastname');
        }
    
        if(Input::has('office_location')) {
            $office = Office::find(Input::get('office_location'));
            if($office) {
                $user->office()->associate($office);
            }
            if(Input::get('office_location')==-1) {
             $user->office_id = null;   
            }
        }

        $user->hobby = Input::get('hobby');
        $user->discipline = Input::get('discipline');

        $user->onboarded = 1;
        
        if($user->save()) {
            return $this->statusResponse(['notice'=>'Profile updated']);
        }
        return $this->statusResponse(['errors'=>$user->errors()->all()]);
    }


    // ------------------------------------------------------------------------
    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store() {

        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());

        if ($user->id) {
            
            $role = $role = Role::where('name', '=', 'Writer')->first();
            $user->attachRole($role);

            Auth::login($user);

            return Redirect::action('UsersController@login')->with('notice', Lang::get('confide::confide.alerts.account_created'));

            Mail::queueOn(
                Config::get('confide::email_queue'),
                Config::get('confide::email_account_confirmation'),
                compact('user'),
                function ($message) use ($user) {
                    $message
                        ->to($user->email, $user->username)
                        ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                }
            );

            //Redirect::action('UsersController@login')
                //->with('notice', Lang::get('confide::confide.alerts.account_created'));
        } else {
            $error = $user->errors()->all(':message');

            return Redirect::action('UsersController@create')
                ->withInput(Input::except('password'))
                ->with('error', $error);
        }
    }

    // ------------------------------------------------------------------------
    public function login() {
        if (Confide::user()) {
            return Redirect::to('/');
        } 
        else {
        	return View::make('site.user.login');
        }
    }

    // ------------------------------------------------------------------------
    public function doLogin()
    {
        $repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input)) {
            return Redirect::intended('/');
        } else {
            if ($repo->isThrottled($input)) {
                $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
            }

            return Redirect::action('UsersController@login')
                ->withInput(Input::except('password'))
                ->with('error', $err_msg);
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@login')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return  Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function doForgotPassword()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
            return Redirect::action('UsersController@doForgotPassword')
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return View::make(Config::get('confide::reset_password_form'))
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@reset_password', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        Confide::logout();
        Auth::logout();
        return Redirect::to('/');
    }
}
