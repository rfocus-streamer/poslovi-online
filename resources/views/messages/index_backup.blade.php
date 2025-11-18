@extends('layouts.app')
<title>Poslovi Online | Poruke</title>
<link href="{{ asset('css/default.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<!-- Toastify JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

@section('content')
<style>
    /* Glavni stilovi */
    .chat-container {
        display: flex;
        height: calc(100vh - 150px);
    }

    .contacts {
        background-color: #f1f1f1;
        overflow-y: auto;
        padding: 10px;
    }

    .chat-box {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-history {
        flex: 1;
        background-color: #ffffff;
        padding: 20px;
        overflow-y: auto;
        min-height: 300px;
    }

    .chat-input {
        padding: 10px;
        background-color: #f1f1f1;
    }

    .chat-message {
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .leftChat {
        background-color: #e1ffe1;
        flex-direction: row-reverse;
        border-radius: 5px;
        padding: 5px;
    }

    .rightChat {
        background-color: #add8e6;
        flex-direction: row-reverse;
        border-radius: 5px;
        padding: 5px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        cursor: pointer;
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.2s;
    }

    .contact-item:hover {
        background-color: #f8f9fa;
    }

    .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-online {
        color: green;
        font-size: 14px;
    }

    .status-offline {
        color: gray;
        font-size: 14px;
    }

    .message-time {
        font-size: 12px;
        color: gray;
        text-align: right;
        margin-left: 10px;
    }

    .message-header {
        display: flex;
        align-items: center;
    }

    .message-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .message-header .status {
        font-size: 12px;
        color: gray;
    }

    .message-body {
        margin-top: 5px;
    }

    .message-content {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .read-status {
        display: inline-block;
        font-size: 0.8em;
    }

    .read-status i {
        cursor: help;
    }

    .conversation-list {
        align-items: flex-end;
        display: inline-flex;
        margin-bottom: 24px;
        max-width: 80%;
        position: relative;
    }

    .conversation-list-right {
        display: flex;
        flex-direction: row-reverse;
        align-items: flex-end;
        margin-bottom: 24px;
        max-width: 80%;
        margin-left: auto;
    }

    .date-separator {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
    }

    .date-separator .date-text {
        padding: 0 10px;
        background-color: white;
        position: relative;
        z-index: 1;
    }

    .date-separator::before,
    .date-separator::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #dee2e6;
        margin: 0 10px;
    }

    .unread-badge {
        font-size: 0.7rem;
        min-width: 1.25rem;
        height: 1.25rem;
        line-height: 1.25rem;
    }

    .contact-item.has-unread {
        font-weight: bold;
    }

    .selected-contact {
        border: 1px solid rgba(255, 0, 0, 0.5) !important;
    }

    .switchBlock {
        position: relative;
        display: inline-block;
        width: 175px;
        height: 20px;
        top: -8px !important;
        cursor: pointer;
    }

    .switchBlock input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .sliderBlock {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 24px;
        background: linear-gradient(to right, #ccc 50%, #4CAF50 50%);
    }

    .sliderBlock:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        border-radius: 50%;
        left: 2px;
        bottom: 1px;
        background-color: white;
        transition: 0.4s;
    }

    .label-textBlock {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        margin-left: 12px;
    }

    .label-textBlock.leftBlock {
        left: 12px;
    }

    .label-textBlock.rightBlock {
        right: 22px;
    }

    .switchBlock input:checked + .sliderBlock {
        background: linear-gradient(to right, #9c1c2c 50%, #ccc 50%);
    }

    .switchBlock input:checked + .sliderBlock:before {
        transform: translateX(0);
    }

    .switchBlock input:not(:checked) + .sliderBlock {
        background: linear-gradient(to right, #ccc 50%, #4CAF50 50%);
    }

    .switchBlock input:not(:checked) + .sliderBlock:before {
        transform: translateX(153px);
    }

    /* Poboljšan vertikalni skrollbar SAMO za kontakt listu */
    .contacts::-webkit-scrollbar {
        width: 12px;
    }

    .contacts::-webkit-scrollbar-thumb {
        background-color: #9c1c2c;
        border-radius: 6px;
        border: 3px solid #f1f1f1;
    }

    .contacts::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }

    /* Firefox */
    .contacts {
        scrollbar-width: auto;
        scrollbar-color: #9c1c2c #f1f1f1;
    }

    /* Stilovi za resizable modal */
    .modal-dialog-resizable {
        resize: both;
        overflow: auto;
        min-width: 800px;
        min-height: 700px;
        max-width: 95vw;
        max-height: 95vh;
    }

    .modal-dialog-resizable .modal-content {
        height: 100%;
        min-height: 100%;
    }

    .modal-dialog.modal-fullscreen {
        resize: none;
        max-width: none;
        max-height: none;
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        margin: 0;
        z-index: 1050; /* Dodaj visok z-index da bude ispred drugih elemenata */
    }

    .modal-header .modal-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #maximizeModal {
        border: none;
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    /* MiroTalk P2P optimizacije */
    .mirotalk-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: #f8f9fa;
        color: #6c757d;
    }

    .permission-request {
        text-align: center;
        padding: 20px;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 5px;
        margin: 10px;
    }

    #mirotalk-iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 8px;
        background: #000;
        min-height: 600px;
    }

    /* Sakrij scrollbar za iframe */
    #mirotalk-iframe::-webkit-scrollbar {
        display: none;
    }

    /* Dodatni stilovi za button stanja */
    .btn-call-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-call-loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .btn-call-active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    /* Stilovi za permisije dugme */
    #testPermissionsBtn.permissions-granted {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    #testPermissionsBtn.permissions-denied {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    #testPermissionsBtn.permissions-unknown {
        background-color: #ffc107;
        border-color: #ffc107;
        color: black;
    }

    /* Pomeranje emotikona desno */
    #emojiPalette {
        position: absolute;  /* Postavljanje emotikona izvan toka dokumenta */
        right: 2px;            /* Poravnanje sa desnim ivicom */
        top: -15px;           /* Podesiti razmak od vrha, po potrebi */
        background-color: #fff; /* Pozadinska boja za bolju vidljivost */
        border: 1px solid #ccc;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        padding: 10px;
        display: none; /* Početno sakrivanje emotikona */
        z-index: 1000; /* Da bude ispred drugih elemenata */
        cursor: pointer;
    }

    #messageForm {
        position: relative;  /* Da bi se pozicionirali emotikoni unutar ove forme */
    }

    iframe {
      /*pointer-events: none;*/
    }

    /* Mobile optimizacija */
    @media (max-width: 768px) {
        #mirotalk-iframe {
            min-height: 400px;
        }

        .modal-dialog-resizable {
            min-width: 95vw;
            min-height: 80vh;
        }

        #videoCallModalLabel {
            font-size: 0.8rem; /* Manja veličina fonta za mobilne */
        }

        #testPermissionsBtn.permissions-granted {
            font-size: 0.5rem; /* Manja veličina fonta za mobilne */
        }
    }

    /* Responsive stilovi */
    @media (max-width: 767px) {
        .chat-container {
            flex-direction: column;
            height: auto;
            min-height: calc(100vh - 200px);
        }

        .contacts {
            height: 200px;
            border-bottom: 1px solid #dee2e6;
            scrollbar-width: thick;
        }

        .contacts::-webkit-scrollbar {
            width: 14px;
        }

        .contacts::-webkit-scrollbar-thumb {
            background: #9c1c2c;
            border-radius: 8px;
            border: 3px solid #f1f1f1;
        }

        .contacts::-webkit-scrollbar-track {
            background: #e9ecef;
            border-radius: 8px;
        }
    }

    @media (min-width: 768px) {
        .contacts {
            min-width: 300px;
            max-width: 350px;
            height: calc(100vh - 200px);
        }

        .chat-box {
            height: calc(100vh - 200px);
        }

        .contacts::-webkit-scrollbar {
            width: 8px;
        }
    }
</style>

<div class="container py-3">
    <div class="row">
        <div class="col-12 p-0">
            <div class="chat-container">
                <!-- Contacts List -->
                <div class="contacts text-secondary">
                    <h6><i class="fa fa-address-book"></i> Tvoji kontakti</h6>
                    <ul class="list-group">
                        @foreach ($contacts as $contact)
                            <li class="list-group-item contact-item" data-user-id="{{ $contact->id }}" onclick="showServices('{{ $contact->id }}', {{$contact->service_titles}})">
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="{{ $contact->is_online ? 'status-online' : 'status-offline' }}">
                                            {{ $contact->is_online ? 'Online' : 'Offline' }}
                                        </div>
                                        <div class="text-warning">
                                            @for ($j = 1; $j <= 5; $j++)
                                                @if ($j <= $contact->stars)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center position-relative">
                                        <img src="{{ Storage::url('user/'.$contact->avatar) }}" alt="{{ $contact->firstname }} {{ $contact->lastname }}" class="contact-avatar me-2">
                                        <div>
                                            <strong>{{ $contact->firstname }} {{ $contact->lastname }}</strong><br>
                                            <small class="text-muted">
                                                @if($contact->last_seen_at)
                                                    Poslednja aktivnost: {{ \Carbon\Carbon::parse($contact->last_seen_at)->format('d.m.Y H:i:s') }}
                                                @endif
                                            </small>
                                        </div>

                                        @php
                                            $unreadCount = $contact->service_titles[0]['unreadCount'] ?? 0;
                                        @endphp

                                        <span data-user-unread-messages-id="{{ $contact->id }}"
                                              class="unread-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                                              style="display: {{ $unreadCount > 0 ? 'inline-block' : 'none' }}">
                                            {{ $unreadCount }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex justify-content-center">
                                    <label class="switchBlock" onclick="event.stopPropagation()">
                                        <input type="checkbox" id="blockSwitch_{{ $contact->id }}"
                                               {{ $contact->blocked ? 'checked' : '' }}
                                               data-contact-id="{{ $contact->id }}"
                                               onchange="toggleBlockStatus(this)">
                                        <span class="sliderBlock"></span>
                                        <span class="label-textBlock leftBlock">Blokiran</span>
                                        <span class="label-textBlock rightBlock">Odblokiran</span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Chat Box -->
                <div class="chat-box">

                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-comment-dots mt-1 text-info"></i> &nbsp;
                            <h6 id="topic" style="margin-top:8px" class="text-decoration-none text-secondary">
                                <span class="text-secondary d-block d-md-none">Izaberi kontakt sa kojim želiš da započneš razgovor.</span>
                            </h6>
                        </div>

                        <button class="btn start-call" data-contactid="" data-serviceid="" style="display: none;" id="buttonCall"  title="Pokreni poziv kroz MiroTalk integraciju">
                            <span class="btn btn-file record-chat-audio chat_optns" data-record="0" data-chat-tab="1">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24" class="select-color" style="color: rgb(198, 77, 83);">
                                    <path fill="#c64d53" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z" style="fill: rgb(198, 77, 83);"></path>
                                </svg>
                                <span class="text-muted">Pozovi</span>
                            </span>
                        </button>
                    </div>

                    <div id="chatHistory" class="chat-history" data-contact-id="0">
                        <h6 class="text-secondary d-none d-md-block">Izaberi kontakt sa kojim želiš da započneš razgovor.</h6>
                    </div>

                    <form id="messageForm" enctype="multipart/form-data" class="border-top">
                        @csrf
                        <input type="hidden" name="service_id" id="service_id" value="">
                        <input type="hidden" name="user_id"  id="user_id" value="">
                        <div class="p-3 bg-light" id="chatArea" style="display: none;">
                            <div class="mb-2">
                                <textarea name="content" id="content" class="form-control" rows="3" placeholder="Unesi poruku..." required></textarea>
                            </div>

                            <!-- Paleta emotikona (skrivena inicijalno) -->
                            <div id="emojiPalette" class="emoji-palette" style="display: none;">
                                <div class="emoji-category">
                                    <span class="emoji" data-emoji="😀">😀</span>
                                    <span class="emoji" data-emoji="😃">😃</span>
                                    <span class="emoji" data-emoji="😄">😄</span>
                                    <span class="emoji" data-emoji="😁">😁</span>
                                    <span class="emoji" data-emoji="😆">😆</span>
                                    <span class="emoji" data-emoji="😅">😅</span>
                                    <span class="emoji" data-emoji="😂">😂</span>
                                    <span class="emoji" data-emoji="🤣">🤣</span>
                                    <span class="emoji" data-emoji="😊">😊</span>
                                    <span class="emoji" data-emoji="😇">😇</span>
                                </div>
                                <div class="emoji-category">
                                    <span class="emoji" data-emoji="😉">😉</span>
                                    <span class="emoji" data-emoji="😌">😌</span>
                                    <span class="emoji" data-emoji="😍">😍</span>
                                    <span class="emoji" data-emoji="🥰">🥰</span>
                                    <span class="emoji" data-emoji="😘">😘</span>
                                    <span class="emoji" data-emoji="😗">😗</span>
                                    <span class="emoji" data-emoji="😙">😙</span>
                                    <span class="emoji" data-emoji="😚">😚</span>
                                    <span class="emoji" data-emoji="😋">😋</span>
                                    <span class="emoji" data-emoji="😛">😛</span>
                                </div>
                                <div class="emoji-category">
                                    <span class="emoji" data-emoji="😎">😎</span>
                                    <span class="emoji" data-emoji="🤓">🤓</span>
                                    <span class="emoji" data-emoji="🧐">🧐</span>
                                    <span class="emoji" data-emoji="🥳">🥳</span>
                                    <span class="emoji" data-emoji="😏">😏</span>
                                    <span class="emoji" data-emoji="😒">😒</span>
                                    <span class="emoji" data-emoji="😞">😞</span>
                                    <span class="emoji" data-emoji="😔">😔</span>
                                    <span class="emoji" data-emoji="😟">😟</span>
                                    <span class="emoji" data-emoji="😕">😕</span>
                                </div>
                                <div class="emoji-category">
                                    <span class="emoji" data-emoji="👍">👍</span>
                                    <span class="emoji" data-emoji="👎">👎</span>
                                    <span class="emoji" data-emoji="❤️">❤️</span>
                                    <span class="emoji" data-emoji="🔥">🔥</span>
                                    <span class="emoji" data-emoji="🎉">🎉</span>
                                    <span class="emoji" data-emoji="🙏">🙏</span>
                                    <span class="emoji" data-emoji="💯">💯</span>
                                    <span class="emoji" data-emoji="✨">✨</span>
                                    <span class="emoji" data-emoji="🌟">🌟</span>
                                    <span class="emoji" data-emoji="✅">✅</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <input type="file" name="attachment" class="form-control form-control-sm w-50">

                                <div class="d-flex gap-2">
                                    <!-- Dugme za emotikone -->
                                    <button type="button" class="btn btn-outline-secondary" id="emojiToggle">
                                        <i class="far fa-smile"></i>
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Pošalji
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Services Modal -->
        <div class="modal fade" id="servicesModal" tabindex="-1" aria-labelledby="servicesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="servicesModalLabel">Izaberi ponudu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" id="servicesList">
                        <!-- Services will be listed here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Video call Modal -->
        <div class="modal fade" id="videoCallModal" tabindex="-1" aria-labelledby="videoCallModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-resizable">
            <div class="modal-content">
              <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="videoCallModalLabel">
                    <i class="fas fa-video me-2"></i>Video Poziv
                </h5>
                <div class="modal-controls">
                  <!-- Dodajemo dugme za testiranje permisija -->
                  <button type="button" class="btn btn-sm btn-outline-warning me-2" id="testPermissionsBtn" title="Proveri dozvole za kameru i mikrofon">
                    <i class="fas fa-camera"></i> Testiraj Dozvole
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-light me-2" id="maximizeModal" style="display: none;">
                    <i class="fas fa-expand"></i>
                  </button>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori" onclick="closeMiroTalkCall()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              </div>
              <div class="modal-body p-0">
                <div id="jitsi-container" style="width: 100%; height: 700px;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Permission Modal -->
        <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header bg-warning">
                <h5 class="modal-title" id="permissionModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Dozvola za kameru i mikrofon
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Zatvori">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="permission-request">
                    <i class="fas fa-camera fa-3x text-warning mb-3"></i>
                    <h4>Dozvolite pristup kameri i mikrofonu</h4>
                    <p class="mb-3">Da biste koristili poziv, morate dozvoliti pristup kameru i mikrofonu.</p>

                    <div class="alert alert-info">
                        <strong>Uputstvo:</strong>
                        <ol class="mt-2">
                            <li>Kliknite na ikonicu kamere/mikrofona u address bar-u vašeg browser-a</li>
                            <li>Izaberite "Allow" ili "Dozvoli" za kameru i mikrofon</li>
                            <li>Osvežite stranicu (F5) ili ponovo pokrenite poziv</li>
                        </ol>
                    </div>

                    <div id="firefoxWarning" style="display: none;">
                        <div class="alert alert-warning">
                            <strong>Napomena za Firefox korisnike:</strong>
                            <p class="mb-0 mt-2">Firefox može imati problema sa pozivima. Ako poziv ne radi, pokušaj da osvežiš stranicu (F5) ili koristi Chrome browser.</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary me-2" onclick="testPermissions()">
                            <i class="fas fa-check"></i> Testiraj dozvole
                        </button>
                        <button class="btn btn-outline-secondary" onclick="openMiroTalkInNewTab()">
                            <i class="fas fa-external-link-alt"></i> Otvori u novom tabu
                        </button>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

    </div>
</div>

<script>
    // Initialize modal variable
    let currentPage = 2;
    let servicesModal = null;
    const currentUser = @json(auth()->user());
    let isLoading = false;
    let directChatService = @json($directChatService);
    const chatHistoryContainer = document.getElementById('chatHistory');
    let mirotalkIframe = null;
    let currentRoomUrl = null;
    window.isCallActive = false;
    let callButtonOriginalHTML = null;
    window.videoCallModal = document.getElementById('videoCallModal');

    document.addEventListener('DOMContentLoaded', function () {
        // Sačuvaj originalno stanje dugmeta
        callButtonOriginalHTML = document.getElementById('buttonCall').innerHTML;

        if (directChatService !== null) {
            document.getElementById('service_id').value = directChatService.id;
            document.getElementById('user_id').value = directChatService.user_id;
            openChat(directChatService.user_id, directChatService.id);
            document.getElementById('topic').innerHTML = directChatService.title;

            // Selektujte dugme
            var button = document.querySelector('.btn.start-call');

            // Promenite vrednosti za data-contactid i data-serviceid
            button.setAttribute('data-contactid', directChatService.user_id);
            button.setAttribute('data-serviceid', directChatService.id);

            document.getElementById('buttonCall').style.display = 'block';
            // Pronađi element sa ID-jem videoCallModalLabel (to je naslov u modal prozoru)
            const titleElement = document.getElementById("videoCallModalLabel");

            // Promeni tekst koji se nalazi u tom elementu
            titleElement.innerHTML = '<i class="fas fa-video me-2"></i> '+directChatService.title;
        }

        // Proveri da li je Firefox i prikaži upozorenje ako jeste
        const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');
        if (isFirefox) {
            document.getElementById('firefoxWarning').style.display = 'block';
        }
    });

    // Funkcija za resetovanje dugmeta na originalno stanje
    window.resetCallButton = function() {
        const button = document.getElementById('buttonCall');
        button.innerHTML = callButtonOriginalHTML;
        button.classList.remove('btn-call-loading', 'btn-call-active');
        button.disabled = false;
        isCallActive = false;

        // Ponovo dodaj event listener
        button.addEventListener('click', handleCallButtonClick);
    }

    // Funkcija za ažuriranje teksta dugmeta
    function updateCallButtonText(text, isLoading = false, isActive = false) {
        const button = document.getElementById('buttonCall');
        const textSpan = button.querySelector('.text-muted');

        if (textSpan) {
            textSpan.textContent = text;
        }

        button.classList.remove('btn-call-loading', 'btn-call-active');

        if (isLoading) {
            button.classList.add('btn-call-loading');
            button.disabled = true;
        } else if (isActive) {
            button.classList.add('btn-call-active');
            button.disabled = true;
        } else {
            button.disabled = false;
        }
    }
</script>

<script type="text/javascript">
    // Funkcija koja uzima poslednju poruku
    async function getLastMessageContact(contactId, serviceId) {
        const apiToken = "{{ $token }}";
        const response = await fetch(`/api/get-last-message-service?contact_id=${contactId}&service_id=${serviceId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data;
    }

    // Funkcija koja prikazuje usluge za kontakt
    async function showServices(contactId, services) {
        const servicesList = document.getElementById('servicesList');
        servicesList.innerHTML = '';

        if (services.length === 0) {
            servicesList.innerHTML = '<p>Nema dostupnih usluga za ovog korisnika</p>';
        } else {
            for (let service of services) {
                try {
                    let contactLastMessage = await getLastMessageContact(contactId, service.service_id);
                    const serviceItem = document.createElement('div');
                    serviceItem.className = 'service-item p-3 border-bottom';
                    serviceItem.style.cursor = 'pointer';

                    let unreadBadge = '';
                    if (contactLastMessage.unread_count > 0) {
                        unreadBadge = `
                            <span data-user-unread-messages-id="${service.service_id}"
                                  class="unread-badge badge bg-danger rounded-pill position-absolute top-50"
                                  style="right: 0.8cm; transform: translateX(50%) translateY(-1.2cm);">
                                ${contactLastMessage.unread_count}
                            </span>
                        `;
                    }

                    serviceItem.innerHTML = `
                        ${unreadBadge}
                        <div class="d-flex justify-content-between align-items-center" data-service-unread-messages-id="${service.service_id}">
                            <strong>${service.service_title}</strong>
                        </div>
                        <small class="text-muted">
                            Poslednja poruka: ${contactLastMessage.last_message_time != null ? formatDate(contactLastMessage.last_message_time) : 'Nema poruka'}
                        </small>
                    `;

                    serviceItem.addEventListener('click', () => {
                        let loadingMsg = '<i class="fa fa-spinner fa-spin"></i> Učitavanje poruka...';
                        document.getElementById('service_id').value = service.service_id;
                        document.getElementById('user_id').value = contactId;
                        document.getElementById('chatHistory').innerHTML = '<div class="text-center p-3">'+loadingMsg+'</div>';
                        openChat(contactId, service.service_id);

                        // Selektujte dugme
                        var button = document.querySelector('.btn.start-call');

                        // Promenite vrednosti za data-contactid i data-serviceid
                        button.setAttribute('data-contactid', contactId);
                        button.setAttribute('data-serviceid', service.service_id);

                        document.getElementById('buttonCall').style.display = 'block';
                        // Pronađi element sa ID-jem videoCallModalLabel (to je naslov u modal prozoru)
                        const titleElement = document.getElementById("videoCallModalLabel");

                        // Promeni tekst koji se nalazi u tom elementu
                        titleElement.innerHTML = '<i class="fas fa-video me-2"></i> '+service.service_title;

                        if (servicesModal) {
                            servicesModal.hide();
                            document.getElementById('topic').innerHTML = service.service_title;
                        }
                    });

                    servicesList.appendChild(serviceItem);
                } catch (error) {
                    console.error('Greška pri dobijanju poslednje poruke za uslugu:', error);
                    const serviceItem = document.createElement('div');
                    serviceItem.className = 'service-item p-3 border-bottom';
                    serviceItem.style.cursor = 'pointer';
                    serviceItem.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center" data-service-unread-messages-id="${service.service_id}">
                            <strong>${service.service_title}</strong>
                        </div>
                        <small class="text-muted">
                            Poslednja poruka: Nema poruka
                        </small>
                    `;
                    servicesList.appendChild(serviceItem);
                }
            }
        }

        if (!servicesModal) {
            servicesModal = new bootstrap.Modal(document.getElementById('servicesModal'));
        }
        servicesModal.show();
    }
</script>

<script type="text/javascript">
// Function to open the chat and display history for the selected contact
async function openChat(contactId, serviceId) {
    //event.preventDefault();
    const apiToken = "{{ $token }}";

    try {
        displayedDates = [];

        const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Greška prilikom preuzimanja poruka');
        }

        const data = await response.json();
        if (data.messages && data.messages.length > 0) {
            chatHistoryContainer.innerHTML = '';

            const chatMessages = data.messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            chatMessages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(message);
                chatHistoryContainer.appendChild(messageDiv)

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting && document.visibilityState === 'visible') {
                            const childNodes = entry.target.childNodes;
                            childNodes.forEach(node => {
                                if (node.nodeType === Node.ELEMENT_NODE && node.hasAttribute('data-message-id')) {
                                    const messageId = node.getAttribute('data-message-id');
                                    const message = data.messages.find(msg => msg.id === parseInt(messageId));
                                    if (message) {
                                        if(!message.read_at){
                                            sendWhisper(message);
                                            sendWhisper(messageId);
                                            selectUnreadMessages(currentUser.id);
                                        }
                                    }
                                }
                            });
                        }
                    });
                }, { threshold: 0.5 });

                if (messageDiv) {
                    observer.observe(messageDiv);
                }
            });
            chatHistoryContainer.setAttribute('data-contact-id', contactId);
        } else {
            document.getElementById('chatHistory').innerHTML = '<p class="text-center">Nema poruka za ovu uslugu.</p>';
        }

        if (data.blockedByHim) {
            const chatArea = document.getElementById('chatArea');
            const messageForm = document.getElementById('messageForm');
            chatArea.style.display = 'none';
            const blockMessage = document.createElement('div');
                  blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
                  blockMessage.textContent = 'Ovaj korisnik te blokirao i ne možeš slati poruke.';
                messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
        } else if (data.blockedByYou) {
                const chatArea = document.getElementById('chatArea');
                const messageForm = document.getElementById('messageForm');
                chatArea.style.display = 'none';
                const blockMessage = document.createElement('div');
                      blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
                      blockMessage.textContent = 'Ti si blokirao ovog korisnika i ne možeš slati poruke.';
                messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
        } else {
                const chatArea = document.getElementById('chatArea');
                chatArea.style.display = 'block';
                const existingBlockMessage = document.querySelector('.alert-warning');
                if (existingBlockMessage) {
                    existingBlockMessage.remove();
                }
        }

        const unreadMessagesDiv = chatHistoryContainer.querySelectorAll('.unreadMessagesDiv');
        if (unreadMessagesDiv.length > 0) {
            const lastUnreadMessage = unreadMessagesDiv[unreadMessagesDiv.length - 1];
            lastUnreadMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        } else {
            chatHistoryContainer.scrollTop = chatHistoryContainer.scrollHeight;
        }
        adjustDateSeparators();
    } catch (error) {
        console.error('Greška prilikom preuzimanja poruka:', error);
    }
}
</script>

<script type="text/javascript">
// Funkcija za generisanje HTML-a poruke
function getMessageHtml(msg) {
    const formattedDate = formatDate(msg.created_at);
    const [date, time] = formattedDate.split(' ');
    let attach = `${msg.attachment_name}`;

    if (msg.sender_id === currentUser.id) {
        let formattedContent = msg.content.replace(/\n/g, '<br>');
        let messageHtml = `
            <div class="conversation-list-right" data-message-id="${msg.id}" data-date="${date}">
                <div class="chat-avatar">
                    <img src="{{ asset('storage/user/') }}/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
                </div>

                <div class="user-chat-content">
                    <div class="conversation-name">
                        <span class="me-1 text-success">
                            <i class="bx bx-check-double bx-check"></i>
                        </span>
                        <strong>${msg.sender.firstname} ${msg.sender.lastname}</strong>
                        <small class="text-muted mb-0 me-2">${time}</small> <small class="read-status"></small>
                    </div>
                    <div class="ctext-wrap">
                        <div class="ctext-wrap-content">
                            <p class="mb-0 rightChat">${formattedContent}</p>
                        </div>
                        ${msg.attachment_path ? `
                        <div class="d-flex justify-content-end mt-1">
                            <small><a href="/${msg.attachment_path}" target="_blank" class="text-decoration-none">
                                        <i class="fa fa-download"></i> ${attach}
                            </a></small>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        if (msg.read_at) {
            messageHtml = messageHtml.replace('<small class="read-status"></small>', `
                <i class="fas fa-check-double text-primary" title="Pročitano ${formatDate(msg.read_at)}"></i>
            `);
            messageHtml = messageHtml.replace('<div class="conversation-list-right"', '<div class="conversation-list-right read"');
        }

        return messageHtml;
    } else {
        let messageHtml = `
                        <div class="conversation-list" data-message-id="${msg.id}" data-date="${date}">
                            <div class="chat-avatar">
                                <img src="{{ asset('storage/user/') }}/${msg.sender.avatar}" alt="You" class="rounded-circle ms-2" style="width: 50px; height: 50px; margin-right:15px;">
                            </div>

                            <div class="user-chat-content">
                                <div class="conversation-name">
                                    <span class="me-1 text-success">
                                        <i class="bx bx-check-double bx-check"></i>
                                    </span>
                                    <strong>${msg.sender.firstname} ${msg.sender.lastname}</strong>
                                    <small class="text-muted mb-0 me-2">${time}</small> <small class="read-status"></small>
                                </div>
                                <div class="ctext-wrap">
                                    <div class="ctext-wrap-content">
                                        <p class="mb-0 leftChat">${msg.content}</p>
                                    </div>
                                    ${msg.attachment_path ? `
                                    <div class="d-flex justify-content-end mt-1">
                                        <small><a href="/${msg.attachment_path}" target="_blank" class="text-decoration-none">
                                                    <i class="fa fa-download"></i> ${attach}
                                        </a></small>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
        if (!msg.read_at) {
            messageHtml = messageHtml.replace('<small class="read-status"></small>', `<small class="read-status unreadMessagesDiv"></small>`);
            messageHtml = messageHtml.replace('<div class="conversation-list-right"', '<div class="conversation-list-left"');
        }

        return messageHtml;
    }
}
</script>

<script type="text/javascript">
function formatDate(dateString) {
    if (!dateString) return 'Invalid date';
    const date = new Date(dateString);
    const options = {
        timeZone: 'Europe/Belgrade',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };
    const formattedDate = new Intl.DateTimeFormat('sr-RS', options).format(date);
    return formattedDate;
}
</script>

<script type="text/javascript">
async function toggleBlockStatus(checkbox) {
    const contactId = checkbox.getAttribute('data-contact-id');
    const isBlocked = checkbox.checked;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const apiUrl = isBlocked
        ? `/api/messages/block/${contactId}`
        : `/api/messages/unblock/${contactId}`;

     const apiToken = "{{ $token }}";

    const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${apiToken}`,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ user_id: contactId }),
    });

    if (!response.ok) {
        throw new Error('Greška prilikom promene statusa blokiranja');
    }

    const data = await response.json();

    if (data.success) {
        Toastify({
                text: isBlocked ? "Korisnik je blokiran." : "Korisnik je odblokiran.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: isBlocked ? "linear-gradient(to right, #ff5f6d, #ffc3a0)" : "linear-gradient(to right, #4CAF50, #8BC34A)",
                stopOnFocus: true,
            }).showToast();

        const chatHistoryDiv = document.getElementById('chatHistory');
        const receiverId = chatHistoryDiv.dataset.contactId;

        if (receiverId == contactId) {
            if (data.blockedByHim) {
                showBlockMessage('Ovaj korisnik te blokirao i ne možeš slati poruke.', 'none');
            } else if (data.blockedByYou) {
                showBlockMessage('Ti si blokirao ovog korisnika i ne možeš slati poruke.', 'none');
            } else {
                showBlockMessage('', 'block');
            }
        }
    }
}

function showBlockMessage(message, displayType) {
    const chatArea = document.getElementById('chatArea');
    const messageForm = document.getElementById('messageForm');
    chatArea.style.display = displayType;
    const existingBlockMessage = document.querySelector('.alert-warning');
    if (existingBlockMessage) {
        existingBlockMessage.remove();
    }

    if (message) {
        const blockMessage = document.createElement('div');
        blockMessage.classList.add('alert', 'alert-warning', 'text-center', 'mt-3');
        blockMessage.textContent = message;
        messageForm.parentNode.insertBefore(blockMessage, messageForm.nextSibling);
    }
}

</script>

<script type="text/javascript">
// Funkcija koja se poziva kada se korisnik skroluje na dno
chatHistoryContainer.addEventListener('scroll', async () => {
    const contactId = chatHistoryContainer.getAttribute('data-contact-id');
    const serviceId = document.getElementById('service_id').value;
    const nearTop = chatHistoryContainer.scrollTop <= 10;

    if (nearTop && !isLoading) {
        isLoading = true;
         const apiToken = "{{ $token }}";

        try {
            const response = await fetch(`/api/get-messages?contact_id=${contactId}&service_id=${serviceId}&page=${currentPage}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            chatMessages = data.messages.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            currentPage += 1;

            chatMessages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.innerHTML = getMessageHtml(msg);
                chatHistoryContainer.insertBefore(messageDiv, chatHistoryContainer.firstChild);
            });

            adjustDateSeparators();

        } catch (error) {
            console.error('Error fetching messages:', error);
        }

        isLoading = false;
    }
});

function adjustDateSeparators() {
    const existingSeparators = chatHistoryContainer.querySelectorAll('.date-separator-container');
    existingSeparators.forEach(separator => separator.remove());

    const displayedDates = [];
    const messages = Array.from(chatHistoryContainer.querySelectorAll('[data-date]'));

    messages.forEach(message => {
        const date = message.getAttribute('data-date');
        if (!displayedDates.includes(date)) {
            displayedDates.push(date);
            const separatorHtml = `
                <div class="date-separator-container mb-1 justify-content-center">
                    <div class="date-separator w-100 text-center">
                        <span class="date-text text-secondary">${date}</span>
                    </div>
                </div>
            `;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = separatorHtml.trim();
            const separatorElement = tempDiv.firstChild;
            message.parentNode.insertBefore(separatorElement, message);
        }
    });
}
</script>

<script>
// Firefox error handling za MiroTalk
function setupFirefoxErrorHandling() {
    // Proveri da li je Firefox
    const isFirefox = navigator.userAgent.toLowerCase().includes('firefox');

    if (isFirefox) {
        console.log('Firefox detected - setting up MiroTalk error handling');

        // Globalni error handler za MiroTalk greške
        window.addEventListener('error', function(e) {
            if (e.filename && (e.filename.includes('speechRecognition.js') || e.filename.includes('videoGrid.js'))) {
                //console.warn('MiroTalk error suppressed for Firefox:', e.message);
                e.preventDefault();
                return true;
            }
        }, true);

        // Error handler za promise greške
        window.addEventListener('unhandledrejection', function(e) {
            if (e.reason && e.reason.toString().includes('getId')) {
                //console.warn('MiroTalk getId promise error suppressed');
                e.preventDefault();
                return true;
            }
        });
    }
}

// Funkcija za dobijanje optimizovanog MiroTalk URL-a
function getOptimizedMiroTalkUrl(roomUrl) {
    let optimizedUrl = roomUrl;
    const params = [
        'hideLeaveBtn=true',
        'minimalUI=true',
        'autoJoin=true',
        'speechRecognition=false',
        'startWithVideoMuted=true',
        'startWithAudioMuted=true',
        'disableSimulcast=true',        // Dodajte i za sve browsere
        'enableNoAudioDetection=false', // Onemogući automatsku detekciju audio problema
        'enableNoisyMicDetection=false', // Onemogući detekciju bučnog mikrofona
        'avatar=0&audio=1&video=0&screen=0&chat=0&hide=0&notify=0'
    ];

    // Proveri da li URL već ima parametre
    if (roomUrl.includes('?')) {
        optimizedUrl += '&' + params.join('&');
    } else {
        optimizedUrl += '?' + params.join('&');
    }

    return optimizedUrl;
}

// Ažurirana MiroTalk P2P integracija
document.addEventListener('DOMContentLoaded', function () {
    let isFullscreen = false;
    const modalEl = document.getElementById('videoCallModal');
    const container = document.getElementById('jitsi-container');
    const maximizeBtn = document.getElementById('maximizeModal');
    const testPermissionsBtn = document.getElementById('testPermissionsBtn');
    const permissionModal = new bootstrap.Modal(document.getElementById('permissionModal'));

    // Provera da li treba automatski otvoriti video modal
    const urlParams = new URLSearchParams(window.location.search);
    const shouldOpenVideoCall = urlParams.get('openVideoCall');
    const contactId = urlParams.get('contactId');
    const serviceId = urlParams.get('serviceId');

    if (shouldOpenVideoCall === 'true') {
        const title = urlParams.get('title').trim();
        const room_url = urlParams.get('room_url');

        // // Ukloni parametre iz URL-a da se ne bi ponovilo pri osvežavanju
        window.history.replaceState({}, document.title, window.location.pathname);

        // // Sačekaj da se stranica potpuno učita
        setTimeout(() => {
            if (contactId && serviceId) {
                // Postavi vrednosti za kontakt i servis
                let loadingMsg = '<i class="fa fa-spinner fa-spin"></i> Učitavanje poruka...';
                document.getElementById('service_id').value = serviceId;
                document.getElementById('user_id').value = contactId;
                document.getElementById('chatHistory').innerHTML = '<div class="text-center p-3">'+loadingMsg+'</div>';
                // Otvori chat pre pokretanja poziva
                openChat(contactId, serviceId);

                // Postavi podatke na dugme za poziv
                const button = document.querySelector('.btn.start-call');
                button.setAttribute('data-contactid', contactId);
                button.setAttribute('data-serviceid', serviceId);
                document.getElementById('buttonCall').style.display = 'block';
                document.getElementById('topic').innerHTML = '<a href="#">'+title+'</a>';
                document.getElementById('videoCallModalLabel').innerText = title;


                // Proveri dozvole pre pokretanja poziva
                checkAndUpdatePermissions().then((hasPermissions) => {
                    if (hasPermissions) {
                        //Koristi optimizovani URL za Firefox
                        const finalUrl = getOptimizedMiroTalkUrl(room_url);
                        openMiroTalkInModal(finalUrl);
                    } else {
                        Toastify({
                            text: "🔒 Molimo proveri dozvole pre pokretanja poziva.",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(to right, #ffa726, #ff9800)",
                        }).showToast();
                        permissionModal.show();
                    }
                });
            }
        }, 500);
    }

    // Postavi Firefox error handling
    setupFirefoxErrorHandling();

    // Funkcija za proveru i ažuriranje statusa dozvola
    async function checkAndUpdatePermissions() {
        try {
            const hasPermissions = await checkMediaPermissionsWithRefresh();

            // Provera da li je korisnik u Firefoxu
            const isFirefox = /firefox/i.test(navigator.userAgent);

            if (isFirefox) {
                // Ako je Firefox, prikazujemo specifičnu poruku
                testPermissionsBtn.classList.remove('permissions-denied', 'permissions-unknown');
                testPermissionsBtn.classList.add('permissions-granted');
                testPermissionsBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Firefox može imati problema sa pozivima';
                testPermissionsBtn.title = "Ako poziv ne radi, pokušaj da osvežiš stranicu (F5) ili koristi Chrome browser.";
                return true;
            } else if (hasPermissions) {
                // Ako je dozvola data
                testPermissionsBtn.classList.remove('permissions-denied', 'permissions-unknown');
                testPermissionsBtn.classList.add('permissions-granted');
                testPermissionsBtn.innerHTML = '<i class="fas fa-check-circle"></i> Dozvole OK';
                testPermissionsBtn.title = "Dozvole za kameru i mikrofon su podešene";
                return true;
            } else {
                // Ako dozvola nije data
                testPermissionsBtn.classList.remove('permissions-granted', 'permissions-unknown');
                testPermissionsBtn.classList.add('permissions-denied');
                testPermissionsBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Problemi sa dozvolama';
                testPermissionsBtn.title = "Klikni da rešiš probleme sa dozvolama";
                return false;
            }
        } catch (error) {
            // Ako dođe do greške
            testPermissionsBtn.classList.remove('permissions-granted', 'permissions-denied');
            testPermissionsBtn.classList.add('permissions-unknown');
            testPermissionsBtn.innerHTML = '<i class="fas fa-question-circle"></i> Proveri Dozvole';
            testPermissionsBtn.title = "Status dozvola nije poznat";
            return false;
        }
    }

    // Provera dozvola sa osvežavanjem
    async function checkMediaPermissionsWithRefresh() {
        try {
            // Prvo probaj da pristupiš medijima da osvežiš permisije
            const stream = await navigator.mediaDevices.getUserMedia({
                audio: true,
                video: true
            });

            // Oslobodi stream odmah
            stream.getTracks().forEach(track => track.stop());

            // Sada proveri status permisija
            let microphoneGranted = false;
            let cameraGranted = false;

            if (navigator.permissions) {
                try {
                    const micPermission = await navigator.permissions.query({ name: 'microphone' });
                    const camPermission = await navigator.permissions.query({ name: 'camera' });
                    microphoneGranted = micPermission.state === 'granted';
                    cameraGranted = camPermission.state === 'granted';
                } catch (e) {
                    // Fallback ako Permission API nije podržan
                    console.log('Permission API not fully supported, using media access as indicator');
                    microphoneGranted = true;
                    cameraGranted = true;
                }
            } else {
                // Ako Permission API nije dostupan, verujemo da su dozvole date
                microphoneGranted = true;
                cameraGranted = true;
            }

            return microphoneGranted && cameraGranted;
        } catch (error) {
            console.log('Media access denied:', error);
            return false;
        }
    }

    // Poboljšana funkcija za testiranje dozvola
    window.testPermissions = async function() {
        const hasAccess = await checkMediaPermissionsWithRefresh();

        // Ažuriraj status dugmeta
        await checkAndUpdatePermissions();

        if (hasAccess) {
            Toastify({
                text: "✅ Dozvole su uspešno podešene!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
            permissionModal.hide();

            // Ponovo pokreni poziv sa osveženim permisijama
            if (currentRoomUrl) {
                openMiroTalkInModal(currentRoomUrl);
            }
        } else {
            Toastify({
                text: "❌ Dozvole nisu dodeljene. Prati uputstvo iznad.",
                duration: 4000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc3a0)",
            }).showToast();

            // Prikaži detaljnije uputstvo u modalu
            const permissionInstructions = document.querySelector('.permission-request');
            if (permissionInstructions && !permissionInstructions.querySelector('#individualPermissions')) {
                permissionInstructions.innerHTML += `
                    <div class="alert alert-danger mt-3" id="individualPermissions">
                        <strong>Trenutni status:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Kamera: <span id="cameraStatus">Nije dozvoljena</span></li>
                            <li>Mikrofon: <span id="microphoneStatus">Nije dozvoljen</span></li>
                        </ul>
                    </div>
                `;

                // Dodaj dinamičku proveru statusa
                checkIndividualPermissions();
            }
        }
    }

    // Funkcija za proveru individualnih dozvola
    async function checkIndividualPermissions() {
        try {
            // Provera kamere
            try {
                const cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
                document.getElementById('cameraStatus').textContent = '✅ Dozvoljena';
                document.getElementById('cameraStatus').className = 'text-success';
                cameraStream.getTracks().forEach(track => track.stop());
            } catch (e) {
                document.getElementById('cameraStatus').textContent = '❌ Nije dozvoljena';
                document.getElementById('cameraStatus').className = 'text-danger';
            }

            // Provera mikrofona
            try {
                const microphoneStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                document.getElementById('microphoneStatus').textContent = '✅ Dozvoljen';
                document.getElementById('microphoneStatus').className = 'text-success';
                microphoneStream.getTracks().forEach(track => track.stop());
            } catch (e) {
                document.getElementById('microphoneStatus').textContent = '❌ Nije dozvoljen';
                document.getElementById('microphoneStatus').className = 'text-danger';
            }
        } catch (error) {
            console.error('Error checking individual permissions:', error);
        }
    }

    // Event listener za dugme za testiranje dozvola u modalu
    testPermissionsBtn.addEventListener('click', function() {
        // Proveri permisije i prikaži modal ako je potrebno
        checkAndUpdatePermissions().then((hasPermissions) => {
            if (!hasPermissions) {
                permissionModal.show();
            } else {
                Toastify({
                    text: "✅ Dozvole su već podešene!",
                    duration: 2000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                }).showToast();
            }
        });
    });

    // Funkcija za rukovanje klikom na dugme poziva
    window.handleCallButtonClick = function(event) {
        event.preventDefault();
        event.stopPropagation();

        const contactId = this.getAttribute('data-contactid');
        const serviceId = this.getAttribute('data-serviceid');

        if (!contactId || !serviceId) {
            Toastify({
                text: "Moraš prvo izabrati kontakt i ponudu pre pokretanja poziva.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc3a0)",
            }).showToast();
            return;
        }

        // Proveri dozvole pre pokretanja poziva
        checkAndUpdatePermissions().then((hasPermissions) => {
            if (hasPermissions) {
                startMiroTalkCall(contactId, serviceId, this);
            } else {
                Toastify({
                    text: "🔒 Molimo proveri dozvole pre pokretanja poziva.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ffa726, #ff9800)",
                }).showToast();
                permissionModal.show();
            }
        });
    }

    // Ažurirana funkcija za pokretanje poziva
    function startMiroTalkCall(contactId, serviceId, buttonElement) {
        const apiToken = "{{ $token }}";

        // Ažuriraj dugme na "Priprema poziva..."
        updateCallButtonText('Priprema poziva...', true, false);

        fetch("{{ route('create.mirotalk.room') }}", {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                contact_id: contactId,
                service_id: serviceId
            })
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(async data => {
            if (!data.success) {
                throw new Error(data.error || 'Došlo je do greške');
            }

            currentRoomUrl = data.roomUrl;

            // Koristi optimizovani URL za Firefox
            const finalUrl = getOptimizedMiroTalkUrl(currentRoomUrl);

            openMiroTalkInModal(finalUrl);

            // Pošalji pozivnicu tek kada se veza uspostavi
            setTimeout(() => {
                if (isCallActive) {
                    sendCallInvitation(contactId, serviceId, data.invitationMessage);
                }
            }, 3000);

        })
        .catch(error => {
            console.error('Greška:', error);
            Toastify({
                text: "Došlo je do greške pri pokretanju poziva: " + error.message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc3a0)",
            }).showToast();
            resetCallButton();
        });
    }

    // Ažurirana funkcija za otvaranje MiroTalk P2P u modalu
    function openMiroTalkInModal(roomUrl) {
        const container = document.getElementById('jitsi-container');
        const maximizeBtn = document.getElementById('maximizeModal');
        maximizeBtn.click();

        container.innerHTML = `
            <div class="mirotalk-loading text-center p-5">
                <i class="fas fa-video fa-spin fa-3x text-primary"></i>
                <p class="mt-3">Uspostavljam video vezu...</p>
                <small class="text-muted">Molimo sačekajte dok se poziv ne uspostavi.</small>
            </div>
        `;

        const iframe = document.createElement('iframe');
        iframe.src = roomUrl;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.allow = 'microphone; fullscreen; display-capture; autoplay;camera;';
       // iframe.deny = 'camera;';
        iframe.allowFullscreen = true;
        iframe.title = 'Video Poziv - MiroTalk';
        iframe.id = 'mirotalk-iframe';

        iframe.onload = function() {
            //console.log('MiroTalk iframe loaded - veza se uspostavlja');
            mirotalkIframe = iframe;
            isCallActive = true;

            // Ažuriraj dugme na "Razgovor u toku" kada se iframe učita
            updateCallButtonText('Razgovor u toku', false, true);

            // Ažuriraj status dozvola
            checkAndUpdatePermissions();

            // Dodaj event listener za praćenje stanja poziva
            window.addEventListener('message', function(event) {
                if (event.data === 'mirotalk-call-ended' || event.data === 'mirotalk-call-failed') {
                    handleCallEnd();
                }
            });
        };

        iframe.onerror = function(error) {
            console.error('MiroTalk iframe error:', error);
            container.innerHTML = `
                <div class="alert alert-danger text-center p-5">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Greška pri učitavanju poziva</h4>
                    <p>Molimo vas da proverite dozvole za kameru i mikrofon.</p>
                    <div class="mt-3">
                        <button class="btn btn-warning me-2" onclick="openMiroTalkInNewTab()">
                            <i class="fas fa-external-link-alt"></i> Otvori u novom tabu
                        </button>
                        <button class="btn btn-info" onclick="retryMiroTalkConnection()">
                            <i class="fas fa-redo"></i> Pokušaj ponovo
                        </button>
                    </div>
                </div>
            `;
            handleCallEnd();
        };

        container.innerHTML = '';
        container.appendChild(iframe);

        const modalEl = document.getElementById('videoCallModal');
        const bootstrapModal = new bootstrap.Modal(modalEl);
        bootstrapModal.show();

        // Ažuriraj status dozvola kada se modal otvori
        checkAndUpdatePermissions();

        // Kada se modal zatvori
        modalEl.addEventListener('hidden.bs.modal', function () {
            handleCallEnd();
        });
    }

    // Funkcija za rukovanje završetkom poziva
    window.handleCallEnd = function() {
        isCallActive = false;
        closeMiroTalkCall();
        resetCallButton();
    }

    // Ažurirana funkcija za zatvaranje MiroTalk poziva
    window.closeMiroTalkCall = function() {
        if (mirotalkIframe) {
            try {
                mirotalkIframe.contentWindow.postMessage({
                    action: 'leaveRoom',
                    type: 'mirotalk-leave'
                }, '*');
            } catch (e) {
                console.log('Cannot send leave command due to CORS');
            }

            const container = document.getElementById('jitsi-container');
            container.innerHTML = '<div class="text-center p-5"><i class="fas fa-video fa-3x text-muted"></i><p class="mt-3">Poziv je završen</p></div>';
            mirotalkIframe = null;
            currentRoomUrl = null;
        }
        resetCallButton();
    }

    // Funkcija za slanje pozivnice (samo kada je veza uspostavljena)
    function sendCallInvitation(contactId, serviceId, invitationMessage) {
        if (!isCallActive) return; // Pošalji samo ako je poziv aktivan

        Toastify({
            text: invitationMessage,
            duration: 4000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
        }).showToast();

    }

    // Funkcija za otvaranje u novom tabu
    window.openMiroTalkInNewTab = function() {
        if (currentRoomUrl) {
            window.open(currentRoomUrl, '_blank', 'width=1200,height=800');
            permissionModal.hide();
            resetCallButton();
        }
    }

    // Funkcija za ponovno pokretanje veze
    window.retryMiroTalkConnection = function() {
        if (currentRoomUrl) {
            const optimizedUrl = getOptimizedMiroTalkUrl(currentRoomUrl);
            openMiroTalkInModal(optimizedUrl);
        }
    }

    // Funkcija za maksimiziranje/minimiziranje modala
    maximizeBtn.addEventListener('click', function() {
        const modalDialog = modalEl.querySelector('.modal-dialog');

        if (!isFullscreen) {
            modalDialog.classList.remove('modal-xl', 'modal-dialog-resizable');
            modalDialog.classList.add('modal-fullscreen');
            maximizeBtn.innerHTML = '<i class="fas fa-compress"></i>';
            isFullscreen = true;
        } else {
            modalDialog.classList.remove('modal-fullscreen');
            modalDialog.classList.add('modal-xl', 'modal-dialog-resizable');
            maximizeBtn.innerHTML = '<i class="fas fa-expand"></i>';
            isFullscreen = false;
        }
    });

    // Dodaj event listener na dugme za poziv
    document.getElementById('buttonCall').addEventListener('click', handleCallButtonClick);

    //console.log('MiroTalk P2P sistem je inicijalizovan');

    // Dodajemo event listener za zatvaranje modala ( izvan okvira )
    $('#videoCallModal').on('hidden.bs.modal', function (e) {
       handleCallEnd();
    });
});

// Funkcionalnost za emotikone
document.addEventListener('DOMContentLoaded', function() {
    const emojiToggle = document.getElementById('emojiToggle');
    const emojiPalette = document.getElementById('emojiPalette');
    const messageTextarea = document.getElementById('content');

    if (emojiToggle && emojiPalette && messageTextarea) {
        // Prikaz/skrivanje palete emotikona
        emojiToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            emojiPalette.style.display = emojiPalette.style.display === 'none' ? 'block' : 'none';
        });

        // Klik na emotikon
        emojiPalette.addEventListener('click', function(e) {
            if (e.target.classList.contains('emoji')) {
                const emoji = e.target.getAttribute('data-emoji');
                insertEmoji(emoji);
            }
        });

        // Funkcija za ubacivanje emotikona u textarea
        function insertEmoji(emoji) {
            const start = messageTextarea.selectionStart;
            const end = messageTextarea.selectionEnd;
            const text = messageTextarea.value;

            messageTextarea.value = text.substring(0, start) + emoji + text.substring(end);
            messageTextarea.focus();
            messageTextarea.selectionStart = messageTextarea.selectionEnd = start + emoji.length;

            // Sakrij paletu nakon ubacivanja emotikona
            emojiPalette.style.display = 'none';

            // Pokreni event za promenu (ako je potrebno)
            messageTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        }

        // Sakrij paletu kada se klikne van nje
        document.addEventListener('click', function(e) {
            if (!emojiPalette.contains(e.target) && e.target !== emojiToggle) {
                emojiPalette.style.display = 'none';
            }
        });

        // Sakrij paletu kada se šalje poruka
        document.getElementById('messageForm').addEventListener('submit', function() {
            emojiPalette.style.display = 'none';
        });

        // Sakrij paletu na Escape tipku
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                emojiPalette.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
