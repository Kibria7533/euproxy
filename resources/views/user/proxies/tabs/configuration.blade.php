<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color: #1e293b;">Proxy Configuration</h5>
                <p class="text-muted mb-4">Configure your application to use {{ $proxyType->name }}. Follow the instructions below based on your use case.</p>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">Connection Details</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td style="width: 200px; padding: 12px 0; color: #64748b; font-weight: 500;">Proxy Host:</td>
                                    <td style="padding: 12px 0;">
                                        <code style="background-color: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem;">proxy.euproxy.com</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-weight: 500;">Proxy Port:</td>
                                    <td style="padding: 12px 0;">
                                        <code style="background-color: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem;">8080</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-weight: 500;">Protocol:</td>
                                    <td style="padding: 12px 0;">
                                        <code style="background-color: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem;">HTTP/HTTPS</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b; font-weight: 500;">Authentication:</td>
                                    <td style="padding: 12px 0;">
                                        <code style="background-color: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem;">Username:Password</code>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info" style="border-radius: 12px; border: none; background-color: #dbeafe; color: #1e40af;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <strong>Note:</strong> You'll receive your username and password after purchasing a plan. Check the "My Subscriptions" tab for your credentials.
                </div>
            </div>
        </div>

        <!-- Code Examples -->
        <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="color: #1e293b;">Code Examples</h5>

                <!-- Python Example -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; color: #3b82f6;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                        Python (Requests)
                    </h6>
                    <pre style="background-color: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 12px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;"><code>import requests

proxies = {
    'http': 'http://username:password@proxy.euproxy.com:8080',
    'https': 'http://username:password@proxy.euproxy.com:8080'
}

response = requests.get('https://api.ipify.org?format=json', proxies=proxies)
print(response.json())</code></pre>
                </div>

                <!-- cURL Example -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; color: #3b82f6;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                        cURL
                    </h6>
                    <pre style="background-color: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 12px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;"><code>curl -x http://proxy.euproxy.com:8080 \
     -U username:password \
     https://api.ipify.org?format=json</code></pre>
                </div>

                <!-- Node.js Example -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; color: #3b82f6;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        Node.js (Axios)
                    </h6>
                    <pre style="background-color: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 12px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;"><code>const axios = require('axios');

const config = {
    proxy: {
        host: 'proxy.euproxy.com',
        port: 8080,
        auth: {
            username: 'username',
            password: 'password'
        }
    }
};

axios.get('https://api.ipify.org?format=json', config)
    .then(response => console.log(response.data));</code></pre>
                </div>

                <!-- PHP Example -->
                <div>
                    <h6 class="fw-semibold mb-3" style="color: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px; vertical-align: middle; color: #3b82f6;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        PHP (cURL)
                    </h6>
                    <pre style="background-color: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 12px; overflow-x: auto; font-size: 0.85rem; line-height: 1.6;"><code>$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.ipify.org?format=json');
curl_setopt($ch, CURLOPT_PROXY, 'proxy.euproxy.com:8080');
curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'username:password');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4" style="border-radius: 16px; border: 1px solid #e2e8f0; background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #1e293b;">Quick Tips</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6; margin-right: 12px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span style="font-size: 0.9rem; color: #475569;">Test your connection with a simple IP check first</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6; margin-right: 12px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span style="font-size: 0.9rem; color: #475569;">Always use HTTPS for secure connections</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6; margin-right: 12px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span style="font-size: 0.9rem; color: #475569;">Monitor your bandwidth usage in the Subscriptions tab</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6; margin-right: 12px; flex-shrink: 0; margin-top: 2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span style="font-size: 0.9rem; color: #475569;">Contact support if you encounter connection issues</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #1e293b;">Need Help?</h6>
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Check out our comprehensive documentation for more examples and troubleshooting guides.</p>
                <a href="{{ route('user.proxies.documentation', $proxyType->slug) }}" class="btn btn-outline-primary w-100" style="border-radius: 8px; font-weight: 500;">
                    View Documentation
                </a>
            </div>
        </div>
    </div>
</div>
