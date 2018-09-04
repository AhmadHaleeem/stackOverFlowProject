<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskQuestionRequest;
use App\Question;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        \DB::enableQueryLog();
        $questions = Question::with('user')->latest()->paginate(10);

        return view('questions.index', compact('questions'));
//        dd(\DB::getQueryLog());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $question = new Question();
        return view('questions.create', compact('question'));
    }


    public function store(AskQuestionRequest $request)
    {
        $request->user()->questions()->create($request->only('title', 'body'));
        return redirect()->route('questions.index')->with('success', 'Your question has been submitted');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        $question->increment('views'); // =>  $question->views = $question->views + 1;

        return view('questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        // Gate is the first way to make privilege
//        if (\Gate::allows('update-question', $question)) {
//        if (\Gate::denies('update-question', $question)) {
//            abort(403, 'Access Denied');
//        }
        // created policy is the second way to make privilege
        $this->authorize('update', $question);
        return view('questions.edit', compact('question'));
    }


    public function update(AskQuestionRequest $request, Question $question)
    {
        // Gates
//        if (\Gate::denies('update-question', $question)) {
//            abort(403, 'Access Denied');
//        }
        // Policy
        $this->authorize('update', $question);
        $question->update($request->only('title', 'body'));
        return redirect()->route('questions.index')->with('success', 'Your question has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        // Gates
//        if (\Gate::denies('delete-question', $question)) {
//            abort(403, 'Access Denied');
//        }

        // Policy
        $this->authorize('delete', $question);
        $question->delete();
        return redirect()->route('questions.index')->with('success', 'Your question has been deleted');
    }
}