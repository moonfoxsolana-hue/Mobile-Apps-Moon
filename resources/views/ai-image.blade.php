<html>
<body>
    <h2>Different Models Comparison</h2>
    <div id="images"></div>
    
    <script src="https://js.puter.com/v2/"></script>
    <script>
        const prompt = "Footsteps echoing just outside the closed door, the lamp never dimming, shadows shifting as the unseen guardian prepares to claim its next victim, tension palpable.";
        const container = document.getElementById('images');
        container.innerHTML = '';

        // GPT Image with low quality
        puter.ai.txt2img(prompt, { model: "ByteDance-Seed/Seedream-4.0"})
            .then(img => {
                const div = document.createElement('div');
                div.innerHTML = '<h3>ByteDance-Seed/Seedream-4.0</h3>';
                div.appendChild(img);
                container.appendChild(div);
            });

        // DALL-E 3 with high quality
        puter.ai.txt2img(prompt, { model: "gemini-2.5-flash-image-preview" })
            .then(img => {
                const div = document.createElement('div');
                div.innerHTML = '<h3>Gemini</h3>';
                div.appendChild(img);
                container.appendChild(div);
            });

        // Gemini 2.5 Flash Image Preview (Nano Banana)
        puter.ai.txt2img(prompt, { model: "Qwen/Qwen-Image" })
                .then(img => {
                    const div = document.createElement('div');
                    div.innerHTML = '<h3>Qwen/Qwen-Image</h3>';
                    div.appendChild(img);
                    container.appendChild(div);
                });

        // Stable Diffusion 3
        puter.ai.txt2img(prompt, { model: "google/imagen-4.0-fast" })
                .then(img => {
                    const div = document.createElement('div');
                    div.innerHTML = '<h3>google/imagen-4.0-fast</h3>';
                    div.appendChild(img);
                    container.appendChild(div);
                });

        // Flux.1 Schnell
        puter.ai.txt2img(prompt, { model: "black-forest-labs/FLUX.1-schnell" })
                .then(img => {
                    const div = document.createElement('div');
                    div.innerHTML = '<h3>Flux.1 Schnell</h3>';
                    div.appendChild(img);
                    container.appendChild(div);
                });
    </script>
</body>
</html>
