{{-- Reusable Algorithmic Flower Bloom Canvas (Python Turtle / Generative Math Art Style) --}}
<canvas id="flower-bloom-canvas"
        class="pointer-events-none absolute inset-0 z-0 h-full w-full select-none"
        aria-hidden="true"></canvas>

<script>
(() => {
    const canvas = document.getElementById('flower-bloom-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = 0;
    let height = 0;
    let dpr = window.devicePixelRatio || 1;
    let animationFrameId = null;

    // Cek preferensi aksesibilitas gerakan
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function resize() {
        const rect = canvas.getBoundingClientRect();
        width = rect.width;
        height = rect.height;
        dpr = window.devicePixelRatio || 1;
        canvas.width = Math.floor(width * dpr);
        canvas.height = Math.floor(height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    class BloomingFlower {
        constructor(isInitial = false) {
            this.reset(isInitial);
        }

        reset(isInitial = false) {
            // Posisi acak dengan padding agar tidak terpotong di tepi
            this.x = 40 + Math.random() * Math.max(width - 80, 50);
            this.y = 40 + Math.random() * Math.max(height - 80, 50);

            // Ukuran dan jumlah kelopak matematika acak (5 - 8 kelopak)
            this.targetSize = 22 + Math.random() * 38;
            this.petals = [5, 6, 7, 8, 10][Math.floor(Math.random() * 5)];
            this.rotation = Math.random() * Math.PI * 2;
            this.rotSpeed = (Math.random() - 0.5) * 0.005;

            this.maxAlpha = 0.35 + Math.random() * 0.45; // 0.35 - 0.80
            this.growSpeed = 0.3 + Math.random() * 0.45;
            this.fadeSpeed = 0.003 + Math.random() * 0.004;

            if (isInitial) {
                // Di awal, sebar bunga pada fase hidup yang berbeda
                this.currentSize = this.targetSize * (0.3 + Math.random() * 0.7);
                this.alpha = this.maxAlpha * (0.4 + Math.random() * 0.6);
                this.state = Math.random() > 0.5 ? 'blooming' : 'growing';
                this.holdFrames = Math.floor(60 + Math.random() * 120);
            } else {
                this.currentSize = 0;
                this.alpha = 0;
                this.state = 'growing';
                this.holdFrames = Math.floor(90 + Math.random() * 180);
            }
        }

        update() {
            this.rotation += this.rotSpeed;

            if (this.state === 'growing') {
                this.currentSize += (this.targetSize - this.currentSize) * 0.045 + this.growSpeed * 0.2;
                this.alpha += (this.maxAlpha - this.alpha) * 0.05 + 0.005;
                if (this.currentSize >= this.targetSize * 0.96) {
                    this.currentSize = this.targetSize;
                    this.alpha = this.maxAlpha;
                    this.state = 'blooming';
                }
            } else if (this.state === 'blooming') {
                this.holdFrames--;
                if (this.holdFrames <= 0) {
                    this.state = 'fading';
                }
            } else if (this.state === 'fading') {
                this.alpha -= this.fadeSpeed;
                if (this.alpha <= 0) {
                    this.alpha = 0;
                    this.reset(false);
                }
            }
        }

        draw(c) {
            if (this.alpha <= 0.01 || this.currentSize <= 1) return;

            c.save();
            c.translate(this.x, this.y);
            c.rotate(this.rotation);

            const step = (Math.PI * 2) / this.petals;

            // Gambar tiap kelopak dengan kurva Bézier geometris (seperti Python Turtle)
            for (let i = 0; i < this.petals; i++) {
                c.beginPath();
                c.rotate(step);
                c.moveTo(0, 0);

                const s = this.currentSize;
                c.bezierCurveTo(
                    -s * 0.35, s * 0.45,
                    -s * 0.30, s * 0.85,
                    0, s
                );
                c.bezierCurveTo(
                    s * 0.30, s * 0.85,
                    s * 0.35, s * 0.45,
                    0, 0
                );

                // Isian kelopak putih transparan
                c.fillStyle = `rgba(255, 255, 255, ${this.alpha * 0.20})`;
                c.fill();

                // Garis tepi kelopak putih tajam berkelas
                c.strokeStyle = `rgba(255, 255, 255, ${this.alpha * 0.75})`;
                c.lineWidth = 1.15;
                c.stroke();
            }

            // Lingkaran luar putik sari
            c.beginPath();
            c.arc(0, 0, this.currentSize * 0.22, 0, Math.PI * 2);
            c.strokeStyle = `rgba(255, 255, 255, ${this.alpha * 0.45})`;
            c.lineWidth = 1;
            c.stroke();

            // Inti putik sari putih berkilau
            c.beginPath();
            c.arc(0, 0, this.currentSize * 0.12, 0, Math.PI * 2);
            c.fillStyle = `rgba(255, 255, 255, ${this.alpha * 0.95})`;
            c.fill();

            c.restore();
        }
    }

    resize();
    window.addEventListener('resize', resize, { passive: true });

    // Inisialisasi kumpulan bunga (8 - 14 bunga bersamaan)
    const flowerCount = Math.max(8, Math.min(15, Math.floor(width / 50)));
    const flowers = [];
    for (let i = 0; i < flowerCount; i++) {
        flowers.push(new BloomingFlower(true));
    }

    if (prefersReducedMotion) {
        // Mode hemat gerak: gambar bunga statis sekali tanpa loop animasi
        flowers.forEach(f => f.draw(ctx));
        return;
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < flowers.length; i++) {
            flowers[i].update();
            flowers[i].draw(ctx);
        }

        animationFrameId = requestAnimationFrame(animate);
    }

    animate();

    window.addEventListener('beforeunload', () => {
        if (animationFrameId) cancelAnimationFrame(animationFrameId);
    });
})();
</script>

