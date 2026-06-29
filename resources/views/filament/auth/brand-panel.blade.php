<div style="height:100%;width:100%;display:flex;flex-direction:column;padding:2.5rem;color:#fff;position:relative;overflow:hidden;background:linear-gradient(145deg,#0c1445 0%,#0f1d6b 45%,#1a2f8a 100%);">

    {{-- Grid overlay --}}
    <div style="position:absolute;inset:0;pointer-events:none;
        background-image:linear-gradient(rgba(255,255,255,0.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.035) 1px,transparent 1px);
        background-size:48px 48px;"></div>

    {{-- Subtle radial glow --}}
    <div style="position:absolute;inset:0;pointer-events:none;
        background:radial-gradient(ellipse 70% 60% at 15% 80%,rgba(59,130,246,0.18) 0%,transparent 70%);"></div>

    {{-- Logo --}}
    <div style="display:flex;align-items:center;gap:0.875rem;position:relative;z-index:1;">
        <div style="width:2.25rem;height:2.25rem;border:1.5px solid rgba(255,255,255,0.75);
                    display:flex;align-items:center;justify-content:center;
                    font-size:1rem;font-weight:700;letter-spacing:-0.02em;flex-shrink:0;">
            S
        </div>
        <div style="width:1px;height:1.75rem;background:rgba(255,255,255,0.25);"></div>
        <div style="font-size:0.65rem;letter-spacing:0.18em;text-transform:uppercase;font-weight:500;line-height:1.4;opacity:0.85;">
            YOUR SAAS<br>
            <span style="opacity:0.55;letter-spacing:0.12em;">BUSINESS PLATFORM</span>
        </div>
    </div>

    {{-- Centre content --}}
    <div style="flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:1;padding:2rem 0;">

        {{-- Badge --}}
        <div style="display:inline-flex;align-items:center;gap:0.5rem;
                    border:1px solid rgba(255,255,255,0.18);border-radius:9999px;
                    background:rgba(255,255,255,0.07);backdrop-filter:blur(4px);
                    padding:0.3rem 0.875rem;font-size:0.72rem;font-weight:500;
                    width:fit-content;margin-bottom:1.75rem;">
            <span style="width:7px;height:7px;border-radius:50%;background:#4ade80;flex-shrink:0;
                         box-shadow:0 0 6px rgba(74,222,128,0.7);"></span>
            Admin Dashboard
        </div>

        {{-- Heading --}}
        <h1 style="font-size:2.125rem;font-weight:700;line-height:1.2;margin:0 0 1rem 0;max-width:22rem;">
            Run your business
            <span style="color:#93c5fd;display:block;">from one place.</span>
        </h1>

        {{-- Subtext --}}
        <p style="font-size:0.9rem;line-height:1.65;opacity:0.65;margin:0 0 2rem 0;max-width:26rem;">
            Manage your CRM, projects, documents and team with a workspace built for speed.
        </p>

        {{-- Feature list --}}
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.75rem;font-size:0.875rem;">
            <li style="display:flex;align-items:center;gap:0.75rem;">
                <span style="width:18px;height:18px;border-radius:50%;background:rgba(74,222,128,0.15);border:1px solid rgba(74,222,128,0.4);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;color:#4ade80;font-weight:700;">✓</span>
                Unified CRM and pipeline
            </li>
            <li style="display:flex;align-items:center;gap:0.75rem;">
                <span style="width:18px;height:18px;border-radius:50%;background:rgba(74,222,128,0.15);border:1px solid rgba(74,222,128,0.4);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;color:#4ade80;font-weight:700;">✓</span>
                Projects and task tracking
            </li>
            <li style="display:flex;align-items:center;gap:0.75rem;">
                <span style="width:18px;height:18px;border-radius:50%;background:rgba(74,222,128,0.15);border:1px solid rgba(74,222,128,0.4);
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;color:#4ade80;font-weight:700;">✓</span>
                All your documents in one place
            </li>
        </ul>
    </div>

    {{-- Footer --}}
    <div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;opacity:0.4;font-size:0.72rem;">
        <span>© 2026 YourSaaS. All rights reserved.</span>
        <span style="cursor:pointer;">Need help?</span>
    </div>
</div>
