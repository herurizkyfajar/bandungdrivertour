@extends('layouts.app', ['title' => 'Rental Duration & Service Terms'])

@section('content')
<div class="dashboard-wrap">
  @include('partials.admin-sidebar')

  <main class="content-card">
    <h2 style="margin:0 0 1rem;">Rental Duration & Service Terms</h2>

    @if(session('success'))
      <div style="background:#dcfce7; color:#166534; padding:.75rem 1rem; border-radius:12px; margin-bottom:1rem; font-weight:600;">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('settings.terms.update') }}">
      @csrf
      @method('PUT')

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:1.25rem; margin-bottom:1rem;">
        <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:700; color:#0f172a;">Terms Content</h3>
        <div id="toolbar" style="margin-bottom:.5rem; display:flex; gap:.35rem; flex-wrap:wrap; padding:.5rem; background:#fff; border:1px solid #cbd5e1; border-radius:12px 12px 0 0; border-bottom:none;">
          <button type="button" class="w-btn" data-cmd="bold" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">B</button>
          <button type="button" class="w-btn" data-cmd="italic" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-style:italic;">I</button>
          <button type="button" class="w-btn" data-cmd="underline" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; text-decoration:underline;">U</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="h3" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">H3</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="h4" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; font-weight:700;">H4</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="p" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">P</button>
          <button type="button" class="w-btn" data-cmd="formatBlock" data-value="blockquote" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Quote</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="insertUnorderedList" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">&#8226; List</button>
          <button type="button" class="w-btn" data-cmd="insertOrderedList" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">1. List</button>
          <div style="width:1px; background:#d1d5db; margin:0 .25rem;"></div>
          <button type="button" class="w-btn" data-cmd="createLink" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Link</button>
          <button type="button" class="w-btn" data-cmd="removeFormat" style="padding:.3rem .6rem; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer;">Clear</button>
        </div>
        <div id="terms_editor" class="w-content" contenteditable="true" style="min-height:400px; padding:1rem; border:1px solid #cbd5e1; border-radius:0 0 12px 12px; background:#fff; outline:none; line-height:1.7; font-size:.95rem;">{!! old('terms_html', $settings->terms_html) !!}</div>
        <input type="hidden" name="terms_html" id="terms_html_input" value="{{ old('terms_html', $settings->terms_html) }}">
      </div>

      <div class="actions">
        <button type="submit" class="btn btn-primary">Save Terms</button>
      </div>
    </form>
  </main>
</div>

<style>
  #terms_editor ul, #terms_editor ol { margin: 4px 0 8px 20px; padding-left: 20px; }
  #terms_editor ul { list-style: disc; }
  #terms_editor ol { list-style: decimal; }
  #terms_editor li { margin-bottom: 2px; }
  #terms_editor ul ul, #terms_editor ol ol, #terms_editor ul ol, #terms_editor ol ul { margin-top: 4px; margin-bottom: 4px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var editor = document.getElementById('terms_editor');
  var input = document.getElementById('terms_html_input');
  var toolbar = document.getElementById('toolbar');
  var savedRange = null;

  editor.addEventListener('mouseup', saveSelection);
  editor.addEventListener('keyup', saveSelection);

  function saveSelection() {
    var sel = window.getSelection();
    if (sel.rangeCount > 0) {
      savedRange = sel.getRangeAt(0);
    }
  }

  function restoreSelection() {
    if (savedRange) {
      var sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(savedRange);
    } else {
      editor.focus();
    }
  }

  toolbar.addEventListener('mousedown', function(e) {
    e.preventDefault();
  });

  toolbar.addEventListener('click', function(e) {
    var btn = e.target.closest('.w-btn');
    if (!btn) return;
    e.preventDefault();

    var cmd = btn.getAttribute('data-cmd');

    if (cmd === 'insertUnorderedList') {
      restoreSelection();
      insertListHTML(false);
    } else if (cmd === 'insertOrderedList') {
      restoreSelection();
      insertListHTML(true);
    } else if (cmd === 'createLink') {
      restoreSelection();
      var url = prompt('Enter URL:');
      if (url) {
        document.execCommand('createLink', false, url);
      }
    } else if (cmd === 'removeFormat') {
      restoreSelection();
      document.execCommand('removeFormat', false, null);
    } else {
      restoreSelection();
      document.execCommand(cmd, false, btn.getAttribute('data-value') || null);
    }

    saveSelection();
  });

  function insertListHTML(ordered) {
    var tag = ordered ? 'ol' : 'ul';
    var sel = window.getSelection();
    if (sel.rangeCount === 0) return;

    var range = sel.getRangeAt(0);
    var selectedHtml = '';

    if (!range.collapsed) {
      var fragment = range.cloneContents();
      var tmp = document.createElement('div');
      tmp.appendChild(fragment);
      selectedHtml = tmp.innerHTML;
    }

    var listEl = document.createElement(tag);
    var liEl = document.createElement('li');
    if (selectedHtml) {
      liEl.innerHTML = selectedHtml;
    } else {
      liEl.innerHTML = '<br>';
    }
    listEl.appendChild(liEl);

    range.deleteContents();
    range.insertNode(listEl);

    range.setStart(liEl, 0);
    range.collapse(true);
    sel.removeAllRanges();
    sel.addRange(range);

    editor.focus();
    saveSelection();
  }

  var form = editor.closest('form');
  if (form) {
    form.addEventListener('submit', function() {
      input.value = editor.innerHTML;
    });
  }
});
</script>
@endsection
