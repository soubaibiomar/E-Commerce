/**
 * Interactive 3D WebGL Product Model Studio (Three.js)
 * Real-time 360 rotation, zoom, materials, color finishes, wireframe, and lighting.
 */

class Product3DViewer {
  constructor(containerId, options = {}) {
    this.container = document.getElementById(containerId);
    if (!this.container) return;

    this.productType = options.type || 'smartphone';
    this.initialColor = options.color || '#94a3b8';
    this.productName = options.name || 'Product';
    
    this.isAutoRotating = true;
    this.isWireframe = false;
    this.currentLightMode = 'studio';

    this.initScene();
    this.initLights();
    this.buildModel();
    this.initControls();
    this.animate();

    window.addEventListener('resize', () => this.onWindowResize());
  }

  initScene() {
    const width = this.container.clientWidth || 450;
    const height = this.container.clientHeight || 380;

    this.scene = new THREE.Scene();
    this.scene.background = new THREE.Color(0xf8fafc);

    this.camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
    this.camera.position.set(0, 0, 7);

    this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    this.renderer.setSize(width, height);
    this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    this.renderer.shadowMap.enabled = true;
    this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
    this.renderer.toneMappingExposure = 1.1;

    this.container.innerHTML = '';
    this.container.appendChild(this.renderer.domElement);

    this.modelGroup = new THREE.Group();
    this.scene.add(this.modelGroup);
  }

  initLights() {
    this.ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
    this.scene.add(this.ambientLight);

    this.keyLight = new THREE.DirectionalLight(0xffffff, 1.2);
    this.keyLight.position.set(5, 8, 5);
    this.keyLight.castShadow = true;
    this.scene.add(this.keyLight);

    this.fillLight = new THREE.DirectionalLight(0xe0e7ff, 0.8);
    this.fillLight.position.set(-5, 3, -4);
    this.scene.add(this.fillLight);

    this.rimLight = new THREE.DirectionalLight(0x6366f1, 0.6);
    this.rimLight.position.set(0, -5, -6);
    this.scene.add(this.rimLight);

    // Floor shadow catcher
    const shadowGeo = new THREE.PlaneGeometry(10, 10);
    const shadowMat = new THREE.ShadowMaterial({ opacity: 0.15 });
    const shadowPlane = new THREE.Mesh(shadowGeo, shadowMat);
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -2.2;
    shadowPlane.receiveShadow = true;
    this.scene.add(shadowPlane);
  }

  initControls() {
    if (typeof THREE.OrbitControls !== 'undefined') {
      this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
      this.controls.enableDamping = true;
      this.controls.dampingFactor = 0.05;
      this.controls.enableZoom = true;
      this.controls.minDistance = 3.5;
      this.controls.maxDistance = 12;
      this.controls.maxPolarAngle = Math.PI / 1.8;
    }
  }

  createRoundedBox(width, height, depth, radius, smoothness) {
    const shape = new THREE.Shape();
    const eps = 0.00001;
    const radiusMinusEps = radius - eps;

    shape.absarc(width / 2 - radius, height / 2 - radius, radiusMinusEps, 0, Math.PI / 2, true);
    shape.absarc(-width / 2 + radius, height / 2 - radius, radiusMinusEps, Math.PI / 2, Math.PI, true);
    shape.absarc(-width / 2 + radius, -height / 2 + radius, radiusMinusEps, Math.PI, Math.PI * 3 / 2, true);
    shape.absarc(width / 2 - radius, -height / 2 + radius, radiusMinusEps, Math.PI * 3 / 2, Math.PI * 2, true);

    const extrudeSettings = {
      depth: depth - radius * 2,
      bevelEnabled: true,
      bevelSegments: smoothness * 2,
      steps: 1,
      bevelSize: radius,
      bevelThickness: radius,
      curveSegments: smoothness * 2
    };

    const geometry = new THREE.ExtrudeGeometry(shape, extrudeSettings);
    geometry.center();
    return geometry;
  }

  buildModel() {
    this.materials = [];
    const pType = this.productType.toLowerCase();

    if (pType.includes('laptop') || pType.includes('macbook') || pType.includes('xps')) {
      this.buildLaptop();
    } else if (pType.includes('headphone') || pType.includes('sony wh') || pType.includes('audio')) {
      this.buildHeadphones();
    } else if (pType.includes('watch') || pType.includes('ultra')) {
      this.buildWatch();
    } else if (pType.includes('playstation') || pType.includes('ps5') || pType.includes('console')) {
      this.buildConsole();
    } else if (pType.includes('chair') || pType.includes('aeron')) {
      this.buildErgonomicChair();
    } else if (pType.includes('book') || pType.includes('habits')) {
      this.buildBook();
    } else {
      this.buildSmartphone();
    }
  }

  buildSmartphone() {
    // 1. Titanium Chassis
    const bodyGeo = this.createRoundedBox(1.9, 3.8, 0.22, 0.16, 8);
    this.bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      metalness: 0.85,
      roughness: 0.25,
      envMapIntensity: 1.2
    });
    this.materials.push(this.bodyMat);
    const body = new THREE.Mesh(bodyGeo, this.bodyMat);
    body.castShadow = true;
    this.modelGroup.add(body);

    // 2. Front OLED Screen Glass
    const screenGeo = new THREE.PlaneGeometry(1.76, 3.65);
    const screenMat = new THREE.MeshStandardMaterial({
      color: 0x050811,
      metalness: 0.95,
      roughness: 0.05,
      emissive: 0x1e1b4b,
      emissiveIntensity: 0.3
    });
    this.materials.push(screenMat);
    const screen = new THREE.Mesh(screenGeo, screenMat);
    screen.position.z = 0.115;
    this.modelGroup.add(screen);

    // Dynamic Island
    const islandGeo = new THREE.CapsuleGeometry ? new THREE.CapsuleGeometry(0.04, 0.25, 4, 8) : new THREE.CylinderGeometry(0.04, 0.04, 0.25, 16);
    const islandMat = new THREE.MeshBasicMaterial({ color: 0x000000 });
    const island = new THREE.Mesh(islandGeo, islandMat);
    island.rotation.z = Math.PI / 2;
    island.position.set(0, 1.55, 0.118);
    this.modelGroup.add(island);

    // 3. Camera Bump Island
    const bumpGeo = this.createRoundedBox(0.95, 0.95, 0.12, 0.12, 6);
    const bumpMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      metalness: 0.9,
      roughness: 0.2
    });
    this.materials.push(bumpMat);
    const bump = new THREE.Mesh(bumpGeo, bumpMat);
    bump.position.set(-0.38, 1.25, -0.14);
    bump.castShadow = true;
    this.modelGroup.add(bump);

    // 3 Triple Camera Lenses
    const lensPositions = [
      [-0.58, 1.45, -0.22],
      [-0.58, 1.05, -0.22],
      [-0.20, 1.25, -0.22]
    ];

    lensPositions.forEach(pos => {
      // Outer metallic ring
      const ringGeo = new THREE.CylinderGeometry(0.16, 0.16, 0.08, 32);
      const ringMat = new THREE.MeshStandardMaterial({ color: 0x334155, metalness: 0.95, roughness: 0.1 });
      const ring = new THREE.Mesh(ringGeo, ringMat);
      ring.rotation.x = Math.PI / 2;
      ring.position.set(pos[0], pos[1], pos[2]);
      this.modelGroup.add(ring);

      // Glass Lens Core
      const glassGeo = new THREE.SphereGeometry(0.12, 16, 16);
      const glassMat = new THREE.MeshPhysicalMaterial({
        color: 0x0a192f,
        metalness: 0.1,
        roughness: 0.0,
        transmission: 0.8,
        reflectivity: 0.9
      });
      const glass = new THREE.Mesh(glassGeo, glassMat);
      glass.position.set(pos[0], pos[1], pos[2] - 0.02);
      this.modelGroup.add(glass);
    });

    // Action Button & Volume Buttons
    const btnGeo = new THREE.BoxGeometry(0.04, 0.22, 0.08);
    const btnMat = new THREE.MeshStandardMaterial({ color: 0x475569, metalness: 0.9, roughness: 0.2 });
    const actionBtn = new THREE.Mesh(btnGeo, btnMat);
    actionBtn.position.set(-0.98, 1.2, 0);
    this.modelGroup.add(actionBtn);

    const powerBtn = new THREE.Mesh(btnGeo, btnMat);
    powerBtn.position.set(0.98, 0.9, 0);
    this.modelGroup.add(powerBtn);
  }

  buildLaptop() {
    // Base chassis
    const baseGeo = this.createRoundedBox(3.4, 2.3, 0.14, 0.08, 6);
    this.bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      metalness: 0.85,
      roughness: 0.28
    });
    this.materials.push(this.bodyMat);
    const base = new THREE.Mesh(baseGeo, this.bodyMat);
    base.rotation.x = Math.PI / 2;
    base.position.y = -0.5;
    base.castShadow = true;
    this.modelGroup.add(base);

    // Keyboard Area
    const kbGeo = new THREE.PlaneGeometry(2.8, 1.1);
    const kbMat = new THREE.MeshStandardMaterial({ color: 0x0f172a, roughness: 0.8 });
    const kb = new THREE.Mesh(kbGeo, kbMat);
    kb.rotation.x = -Math.PI / 2;
    kb.position.set(0, -0.42, -0.3);
    this.modelGroup.add(kb);

    // Trackpad
    const padGeo = new THREE.PlaneGeometry(1.3, 0.7);
    const padMat = new THREE.MeshStandardMaterial({ color: 0x334155, metalness: 0.7, roughness: 0.3 });
    const pad = new THREE.Mesh(padGeo, padMat);
    pad.rotation.x = -Math.PI / 2;
    pad.position.set(0, -0.42, 0.65);
    this.modelGroup.add(pad);

    // Screen Lid (Open at 115 degrees)
    const lidGroup = new THREE.Group();
    lidGroup.position.set(0, -0.42, -1.15);

    const lidGeo = this.createRoundedBox(3.4, 2.2, 0.08, 0.08, 6);
    const lid = new THREE.Mesh(lidGeo, this.bodyMat);
    lid.position.set(0, 1.1, 0);
    lidGroup.add(lid);

    const screenGeo = new THREE.PlaneGeometry(3.2, 2.0);
    const screenMat = new THREE.MeshStandardMaterial({
      color: 0x0b132b,
      roughness: 0.1,
      metalness: 0.9,
      emissive: 0x1e3a8a,
      emissiveIntensity: 0.4
    });
    const screen = new THREE.Mesh(screenGeo, screenMat);
    screen.position.set(0, 1.1, 0.045);
    lidGroup.add(screen);

    lidGroup.rotation.x = -Math.PI * 0.18; // Angled open
    this.modelGroup.add(lidGroup);
  }

  buildHeadphones() {
    this.bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      roughness: 0.4,
      metalness: 0.6
    });
    this.materials.push(this.bodyMat);

    // Headband
    const curve = new THREE.EllipseCurve(0, 0, 1.5, 1.5, 0, Math.PI, false, 0);
    const points = curve.getPoints(50);
    const bandGeo = new THREE.TubeGeometry(new THREE.CatmullRomCurve3(points.map(p => new THREE.Vector3(p.x, p.y, 0))), 64, 0.12, 12, false);
    const band = new THREE.Mesh(bandGeo, this.bodyMat);
    band.position.y = 0.5;
    this.modelGroup.add(band);

    // Left Ear Cup
    const cupGeo = new THREE.CylinderGeometry(0.7, 0.75, 0.4, 32);
    const leftCup = new THREE.Mesh(cupGeo, this.bodyMat);
    leftCup.rotation.z = Math.PI / 2;
    leftCup.position.set(-1.5, 0.4, 0);
    leftCup.castShadow = true;
    this.modelGroup.add(leftCup);

    // Right Ear Cup
    const rightCup = leftCup.clone();
    rightCup.position.set(1.5, 0.4, 0);
    this.modelGroup.add(rightCup);

    // Soft Cushion Rings
    const cushionMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.9 });
    const cushionGeo = new THREE.TorusGeometry(0.65, 0.14, 16, 32);
    const leftCushion = new THREE.Mesh(cushionGeo, cushionMat);
    leftCushion.rotation.y = Math.PI / 2;
    leftCushion.position.set(-1.25, 0.4, 0);
    this.modelGroup.add(leftCushion);

    const rightCushion = leftCushion.clone();
    rightCushion.position.set(1.25, 0.4, 0);
    this.modelGroup.add(rightCushion);
  }

  buildWatch() {
    this.bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      metalness: 0.9,
      roughness: 0.2
    });
    this.materials.push(this.bodyMat);

    // Titanium Watch Case
    const caseGeo = this.createRoundedBox(1.6, 1.9, 0.45, 0.2, 8);
    const watchCase = new THREE.Mesh(caseGeo, this.bodyMat);
    watchCase.castShadow = true;
    this.modelGroup.add(watchCase);

    // Sapphire Display
    const displayGeo = new THREE.PlaneGeometry(1.35, 1.65);
    const displayMat = new THREE.MeshStandardMaterial({
      color: 0x050814,
      emissive: 0xff6600,
      emissiveIntensity: 0.35,
      metalness: 0.9,
      roughness: 0.1
    });
    const display = new THREE.Mesh(displayGeo, displayMat);
    display.position.z = 0.23;
    this.modelGroup.add(display);

    // Digital Crown
    const crownGeo = new THREE.CylinderGeometry(0.2, 0.2, 0.15, 24);
    const crownMat = new THREE.MeshStandardMaterial({ color: 0xea580c, metalness: 0.9, roughness: 0.2 });
    const crown = new THREE.Mesh(crownGeo, crownMat);
    crown.rotation.z = Math.PI / 2;
    crown.position.set(0.85, 0.3, 0);
    this.modelGroup.add(crown);

    // Ocean Band Straps (Top & Bottom)
    const strapGeo = new THREE.BoxGeometry(1.2, 1.8, 0.15);
    const strapMat = new THREE.MeshStandardMaterial({ color: 0xf97316, roughness: 0.6 });
    const topStrap = new THREE.Mesh(strapGeo, strapMat);
    topStrap.position.set(0, 1.6, -0.1);
    this.modelGroup.add(topStrap);

    const btmStrap = new THREE.Mesh(strapGeo, strapMat);
    btmStrap.position.set(0, -1.6, -0.1);
    this.modelGroup.add(btmStrap);
  }

  buildConsole() {
    this.bodyMat = new THREE.MeshStandardMaterial({ color: 0xf8fafc, roughness: 0.3, metalness: 0.2 });
    this.materials.push(this.bodyMat);

    // White Outer Wings
    const wingGeo = new THREE.BoxGeometry(0.2, 3.6, 2.2);
    const leftWing = new THREE.Mesh(wingGeo, this.bodyMat);
    leftWing.position.x = -0.4;
    leftWing.castShadow = true;
    this.modelGroup.add(leftWing);

    const rightWing = leftWing.clone();
    rightWing.position.x = 0.4;
    this.modelGroup.add(rightWing);

    // Black Core Body
    const coreGeo = new THREE.BoxGeometry(0.65, 3.4, 2.0);
    const coreMat = new THREE.MeshStandardMaterial({ color: 0x090d16, roughness: 0.2, metalness: 0.8 });
    const core = new THREE.Mesh(coreGeo, coreMat);
    this.modelGroup.add(core);

    // Blue LED Glow Strip
    const ledGeo = new THREE.BoxGeometry(0.04, 3.2, 0.04);
    const ledMat = new THREE.MeshBasicMaterial({ color: 0x38bdf8 });
    const led = new THREE.Mesh(ledGeo, ledMat);
    led.position.set(-0.3, 0, 1.02);
    this.modelGroup.add(led);
  }

  buildErgonomicChair() {
    this.bodyMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, roughness: 0.7, metalness: 0.4 });
    this.materials.push(this.bodyMat);

    // Mesh Backrest
    const backGeo = this.createRoundedBox(1.8, 2.2, 0.15, 0.15, 6);
    const back = new THREE.Mesh(backGeo, this.bodyMat);
    back.position.set(0, 0.8, -0.5);
    back.rotation.x = -0.15;
    this.modelGroup.add(back);

    // Seat Pan
    const seatGeo = this.createRoundedBox(1.9, 1.8, 0.18, 0.18, 6);
    const seat = new THREE.Mesh(seatGeo, this.bodyMat);
    seat.position.set(0, -0.2, 0);
    seat.rotation.x = Math.PI / 2;
    this.modelGroup.add(seat);

    // Armrests
    const armGeo = new THREE.BoxGeometry(0.25, 0.8, 0.1);
    const armMat = new THREE.MeshStandardMaterial({ color: 0x0f172a, roughness: 0.8 });
    const leftArm = new THREE.Mesh(armGeo, armMat);
    leftArm.position.set(-1.1, 0.3, -0.1);
    this.modelGroup.add(leftArm);

    const rightArm = leftArm.clone();
    rightArm.position.set(1.1, 0.3, -0.1);
    this.modelGroup.add(rightArm);

    // Center Column & 5-Star Base
    const poleGeo = new THREE.CylinderGeometry(0.12, 0.12, 1.2, 16);
    const poleMat = new THREE.MeshStandardMaterial({ color: 0x64748b, metalness: 0.9, roughness: 0.2 });
    const pole = new THREE.Mesh(poleGeo, poleMat);
    pole.position.set(0, -0.9, 0);
    this.modelGroup.add(pole);
  }

  buildBook() {
    this.bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(this.initialColor),
      roughness: 0.6,
      metalness: 0.1
    });
    this.materials.push(this.bodyMat);

    // Hardcover Book
    const coverGeo = this.createRoundedBox(2.2, 3.2, 0.45, 0.05, 4);
    const book = new THREE.Mesh(coverGeo, this.bodyMat);
    book.castShadow = true;
    this.modelGroup.add(book);

    // White Pages Block inside
    const pagesGeo = new THREE.BoxGeometry(2.05, 3.05, 0.38);
    const pagesMat = new THREE.MeshStandardMaterial({ color: 0xfefefe, roughness: 0.9 });
    const pages = new THREE.Mesh(pagesGeo, pagesMat);
    pages.position.x = 0.08;
    this.modelGroup.add(pages);
  }

  setColor(hexColor) {
    if (this.bodyMat) {
      this.bodyMat.color.set(hexColor);
    }
  }

  toggleRotation() {
    this.isAutoRotating = !this.isAutoRotating;
    return this.isAutoRotating;
  }

  toggleWireframe() {
    this.isWireframe = !this.isWireframe;
    this.materials.forEach(m => {
      m.wireframe = this.isWireframe;
    });
    return this.isWireframe;
  }

  setLighting(mode) {
    this.currentLightMode = mode;
    if (mode === 'neon') {
      this.scene.background = new THREE.Color(0x0a0f1d);
      this.ambientLight.intensity = 0.3;
      this.keyLight.color.setHex(0x00f0ff);
      this.fillLight.color.setHex(0xff007f);
      this.rimLight.color.setHex(0x7928ca);
    } else if (mode === 'sunset') {
      this.scene.background = new THREE.Color(0xfff7ed);
      this.ambientLight.intensity = 0.8;
      this.keyLight.color.setHex(0xfb923c);
      this.fillLight.color.setHex(0xf43f5e);
      this.rimLight.color.setHex(0xfef08a);
    } else {
      // Studio Default
      this.scene.background = new THREE.Color(0xf8fafc);
      this.ambientLight.intensity = 0.7;
      this.keyLight.color.setHex(0xffffff);
      this.fillLight.color.setHex(0xe0e7ff);
      this.rimLight.color.setHex(0x6366f1);
    }
  }

  resetView() {
    this.camera.position.set(0, 0, 7);
    if (this.controls) {
      this.controls.reset();
    }
  }

  onWindowResize() {
    if (!this.container) return;
    const width = this.container.clientWidth;
    const height = this.container.clientHeight || 380;
    this.camera.aspect = width / height;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(width, height);
  }

  animate() {
    requestAnimationFrame(() => this.animate());

    if (this.isAutoRotating && this.modelGroup) {
      this.modelGroup.rotation.y += 0.008;
    }

    if (this.controls) {
      this.controls.update();
    }

    this.renderer.render(this.scene, this.camera);
  }
}

window.Product3DViewer = Product3DViewer;
