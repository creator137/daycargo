@once
    @push('scripts')
        <script>
            (function() {
                function activate(root, key, pushHash = true) {
                    const links = root.querySelectorAll('[role="tab"][data-tab]');
                    const panels = root.querySelectorAll('[data-tab-panel]');

                    links.forEach(a => {
                        const on = a.getAttribute('data-tab') === key;
                        a.setAttribute('aria-selected', on ? 'true' : 'false');
                        a.classList.toggle('border-indigo-600', on);
                        a.classList.toggle('text-indigo-700', on);
                        a.classList.toggle('font-medium', on);
                        a.classList.toggle('border-transparent', !on);
                        a.classList.toggle('text-slate-600', !on);
                    });

                    let shown = false;
                    panels.forEach(p => {
                        const on = p.getAttribute('data-tab-panel') === key;
                        p.classList.toggle('hidden', !on);
                        if (on) shown = true;
                    });

                    // если панели с таким ключом нет — включим первую
                    if (!shown && links[0]) {
                        key = links[0].getAttribute('data-tab');
                        panels.forEach(p => p.classList.add('hidden'));
                        activate(root, key, false);
                    }

                    root.setAttribute('data-active', key || '');

                    if (pushHash && key) {
                        if (history.pushState) history.pushState(null, '', '#' + key);
                        else location.hash = key;
                    }
                }

                function init(root) {
                    const links = root.querySelectorAll('[role="tab"][data-tab]');
                    if (!links.length) return;

                    // Скрыть все, дальше включим нужную
                    root.querySelectorAll('[data-tab-panel]').forEach(p => p.classList.add('hidden'));

                    const hash = (location.hash || '').slice(1);
                    const initial = (hash && root.querySelector('[role="tab"][data-tab="' + hash + '"]')) ?
                        hash :
                        (root.getAttribute('data-active') || links[0].getAttribute('data-tab'));

                    activate(root, initial, false);
                }

                document.addEventListener('click', e => {
                    const link = e.target.closest('[role="tab"][data-tab]');
                    if (!link) return;
                    const root = link.closest('[data-tabs-root]');
                    if (!root) return;
                    e.preventDefault();
                    activate(root, link.getAttribute('data-tab'), true);
                });

                window.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('[data-tabs-root]').forEach(init);
                });

                window.addEventListener('hashchange', () => {
                    const key = (location.hash || '').slice(1);
                    if (!key) return;
                    document.querySelectorAll('[data-tabs-root]').forEach(root => {
                        if (root.querySelector('[role="tab"][data-tab="' + key + '"]')) {
                            activate(root, key, false);
                        }
                    });
                });
            })
            ();
        </script>
    @endpush
@endonce
