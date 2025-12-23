<!doctype html>
<html lang="ru">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Доступ ограничен</title>
        @if (function_exists('vite'))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                position: relative;
                overflow: hidden;
            }

            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background:
                    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
                pointer-events: none;
            }

            .container {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            .content-box {
                background: white;
                border-radius: 24px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                padding: 3rem;
                max-width: 600px;
                width: 100%;
                text-align: center;
                animation: slideUp 0.6s ease-out;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .truck-container {
                margin-bottom: 2rem;
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            .truck {
                width: 200px;
                height: auto;
                margin: 0 auto;
            }

            h1 {
                font-size: 2rem;
                font-weight: 700;
                color: #1a202c;
                margin: 0 0 1rem 0;
            }

            p {
                font-size: 1.125rem;
                color: #4a5568;
                margin: 0 0 2rem 0;
                line-height: 1.6;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.875rem 2rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s, box-shadow 0.2s;
                text-decoration: none;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            }

            .btn:active {
                transform: translateY(0);
            }

            /* Грустная грузовая машина SVG */
            .sad-truck {
                fill: none;
                stroke-linecap: round;
                stroke-linejoin: round;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="content-box">
                <div class="truck-container">
                    <svg class="truck" viewBox="0 0 200 150" xmlns="http://www.w3.org/2000/svg">
                        <!-- Кузов грузовика -->
                        <rect x="70" y="50" width="80" height="40" fill="#667eea" stroke="#5a67d8"
                            stroke-width="2" />

                        <!-- Кабина -->
                        <path d="M 50 70 L 50 50 L 70 50 L 70 90 L 50 90 Z" fill="#764ba2" stroke="#6b46c1"
                            stroke-width="2" />

                        <!-- Окно кабины -->
                        <rect x="52" y="55" width="16" height="12" fill="#e0e7ff" stroke="#5a67d8"
                            stroke-width="1" />

                        <!-- Грустное лицо на кабине -->
                        <circle cx="59" cy="63" r="1.5" fill="#1a202c" />
                        <circle cx="64" cy="63" r="1.5" fill="#1a202c" />
                        <path d="M 56 70 Q 61.5 68 67 70" class="sad-truck" stroke="#1a202c" stroke-width="1.5" />

                        <!-- Решетка радиатора -->
                        <rect x="52" y="70" width="16" height="8" fill="#9f7aea" stroke="#6b46c1"
                            stroke-width="1" />
                        <line x1="54" y1="72" x2="54" y2="76" stroke="#e0e7ff"
                            stroke-width="0.5" />
                        <line x1="57" y1="72" x2="57" y2="76" stroke="#e0e7ff"
                            stroke-width="0.5" />
                        <line x1="60" y1="72" x2="60" y2="76" stroke="#e0e7ff"
                            stroke-width="0.5" />
                        <line x1="63" y1="72" x2="63" y2="76" stroke="#e0e7ff"
                            stroke-width="0.5" />
                        <line x1="66" y1="72" x2="66" y2="76" stroke="#e0e7ff"
                            stroke-width="0.5" />

                        <!-- Колеса -->
                        <circle cx="65" cy="95" r="10" fill="#2d3748" stroke="#1a202c" stroke-width="2" />
                        <circle cx="65" cy="95" r="5" fill="#4a5568" stroke="#2d3748" stroke-width="1" />

                        <circle cx="135" cy="95" r="10" fill="#2d3748" stroke="#1a202c" stroke-width="2" />
                        <circle cx="135" cy="95" r="5" fill="#4a5568" stroke="#2d3748" stroke-width="1" />

                        <!-- Подвеска -->
                        <line x1="50" y1="90" x2="150" y2="90" stroke="#2d3748"
                            stroke-width="3" />

                        <!-- Детали кузова -->
                        <line x1="80" y1="50" x2="80" y2="90" stroke="#5a67d8"
                            stroke-width="1" opacity="0.5" />
                        <line x1="95" y1="50" x2="95" y2="90" stroke="#5a67d8"
                            stroke-width="1" opacity="0.5" />
                        <line x1="110" y1="50" x2="110" y2="90" stroke="#5a67d8"
                            stroke-width="1" opacity="0.5" />
                        <line x1="125" y1="50" x2="125" y2="90" stroke="#5a67d8"
                            stroke-width="1" opacity="0.5" />
                        <line x1="140" y1="50" x2="140" y2="90" stroke="#5a67d8"
                            stroke-width="1" opacity="0.5" />

                        <!-- Знак "СТОП" возле машины -->
                        <circle cx="30" cy="75" r="12" fill="#e53e3e" stroke="#c53030"
                            stroke-width="2" />
                        <text x="30" y="80" font-size="10" font-weight="bold" fill="white"
                            text-anchor="middle">СТОП</text>
                    </svg>
                </div>

                <h1>Доступ ограничен</h1>
                <p>
                    К сожалению, у вас недостаточно прав для просмотра этой страницы.
                    Пожалуйста, обратитесь к администратору, если считаете, что это ошибка.
                </p>

                <button type="button" onclick="history.length > 1 ? history.back() : (window.location.href = '/')"
                    class="btn">
                    Вернуться назад
                </button>
            </div>
        </div>
    </body>

</html>
