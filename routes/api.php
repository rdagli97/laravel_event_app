<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRequestController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    // User
    Route::post('/update/pp', [UserController::class, 'updateUser']);
    // Event
    Route::get('/event/get/joined', [EventController::class, 'getMyJoinedEvents']);
    Route::post('/create/event', [EventController::class, 'createEvent']);
    Route::get('/current/user/events', [EventController::class, 'getCurrentUserEvents']);
    Route::get('/all/events', [EventController::class, 'getAllEvents']);
    Route::delete('/event/{id}/delete', [EventController::class, 'deleteEvent']);
    // Conversation
    Route::post('/conversation/create/{id}', [ConversationController::class, 'createConversation']);
    Route::get('/conversation/current/user', [ConversationController::class, 'getMyConversations']);
    // Message
    Route::post('/message/{id}/send', [MessageController::class, 'sendMessage']);
    // FriendRequest
    Route::get('/friend_requests/current/user', [FriendRequestController::class, 'getMyFriendRequests']);
    Route::post('/friend_request/{id}/create', [FriendRequestController::class, 'createFriendRequest']);
    Route::delete('/friend_request/{id}/delete', [FriendRequestController::class, 'deleteFriendRequest']);
    // Friend
    Route::get('/friends/get/current/user', [FriendController::class, 'getMyFriends']);
    Route::post('/friends/{id}/create', [FriendController::class, 'createFriend']);
    Route::delete('/friends/{id}/delete', [FriendController::class, 'deleteFriend']);
    // Event Request
    Route::get('/event/get/current/user/requests', [EventRequestController::class, 'getMyEventRequests']);
    Route::post('/event/request/{id}/create', [EventRequestController::class, 'createEventRequest']);
    Route::delete('/event/request/{id}/delete', [EventRequestController::class, 'deleteEventRequest']);
    // Member
    Route::get('/event/{id}/get/members', [MemberController::class, 'getMembersOfAnEvent']);
    Route::post('/member/{id}/create', [MemberController::class, 'createMemberOfAnEvent']);
    Route::delete('/event/{eventId}/member/{userId}/delete', [MemberController::class, 'deleteMemberFromEvent']);
    // Comments
    Route::get('/comment/get/current/user', [CommentController::class, 'getMyComments']);
    Route::post('/comment/user/{id}/create', [CommentController::class, 'addCommentAfterEvent']);
    Route::get('/comments/rating/get', [CommentController::class, 'getAverageRating']);
});
