'use client';

import React, { useEffect, useRef, useState } from 'react';
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { RotateCw, Sun, Box, RefreshCcw } from 'lucide-react';

interface Product3DStudioProps {
  productType?: 'smartphone' | 'laptop' | 'headphone' | 'watch' | 'console' | 'chair' | 'book';
  productName?: string;
  initialColor?: string;
}

export default function Product3DStudio({
  productType = 'smartphone',
  productName = 'Precision Flagship',
  initialColor = '#94a3b8',
}: Product3DStudioProps) {
  const mountRef = useRef<HTMLDivElement>(null);
  const [currentColor, setCurrentColor] = useState(initialColor);
  const [isAutoRotate, setIsAutoRotate] = useState(true);
  const [isWireframe, setIsWireframe] = useState(false);
  const [lightMode, setLightMode] = useState<'studio' | 'neutral' | 'warm'>('studio');

  const sceneRef = useRef<THREE.Scene | null>(null);
  const rendererRef = useRef<THREE.WebGLRenderer | null>(null);
  const cameraRef = useRef<THREE.PerspectiveCamera | null>(null);
  const controlsRef = useRef<OrbitControls | null>(null);
  const modelGroupRef = useRef<THREE.Group | null>(null);
  const bodyMaterialRef = useRef<THREE.MeshStandardMaterial | null>(null);
  const lightsRef = useRef<{
    ambient: THREE.AmbientLight;
    key: THREE.DirectionalLight;
    fill: THREE.DirectionalLight;
    rim: THREE.DirectionalLight;
  } | null>(null);

  useEffect(() => {
    if (!mountRef.current) return;

    const width = mountRef.current.clientWidth;
    const height = mountRef.current.clientHeight || 420;

    // Scene
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0b0f17);
    sceneRef.current = scene;

    // Camera
    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
    camera.position.set(0, 0.4, 7.5);
    cameraRef.current = camera;

    // Renderer with high precision shadow maps & tone mapping
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.1;

    mountRef.current.innerHTML = '';
    mountRef.current.appendChild(renderer.domElement);
    rendererRef.current = renderer;

    // Orbit Controls with smooth damping
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.minDistance = 3.5;
    controls.maxDistance = 14;
    controls.maxPolarAngle = Math.PI / 2 + 0.1;
    controlsRef.current = controls;

    // Studio Lighting Grid (Key, Fill, Rim, Ambient)
    const ambient = new THREE.AmbientLight(0xffffff, 0.85);
    scene.add(ambient);

    const key = new THREE.DirectionalLight(0xffffff, 2.2);
    key.position.set(5, 8, 6);
    key.castShadow = true;
    key.shadow.mapSize.width = 1024;
    key.shadow.mapSize.height = 1024;
    scene.add(key);

    const fill = new THREE.DirectionalLight(0x93c5fd, 1.0);
    fill.position.set(-6, 3, -4);
    scene.add(fill);

    const rim = new THREE.DirectionalLight(0xffffff, 1.4);
    rim.position.set(0, 6, -6);
    scene.add(rim);

    lightsRef.current = { ambient, key, fill, rim };

    // Ground Shadow Receiver Plane
    const groundGeo = new THREE.PlaneGeometry(30, 30);
    const groundMat = new THREE.ShadowMaterial({ opacity: 0.35 });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -2.2;
    ground.receiveShadow = true;
    scene.add(ground);

    // Dynamic Geometry Builder based on productType
    const modelGroup = new THREE.Group();
    scene.add(modelGroup);
    modelGroupRef.current = modelGroup;

    // PBR Physical Body Material
    const bodyMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(currentColor),
      metalness: 0.85,
      roughness: 0.22,
    });
    bodyMaterialRef.current = bodyMat;

    const createRoundedBox = (w: number, h: number, d: number, r: number) => {
      const shape = new THREE.Shape();
      const x = -w / 2;
      const y = -h / 2;
      shape.moveTo(x + r, y);
      shape.lineTo(x + w - r, y);
      shape.quadraticCurveTo(x + w, y, x + w, y + r);
      shape.lineTo(x + w, y + h - r);
      shape.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
      shape.lineTo(x + r, y + h);
      shape.quadraticCurveTo(x, y + h, x, y + h - r);
      shape.lineTo(x, y + r);
      shape.quadraticCurveTo(x, y, x + r, y);

      const extrudeSettings = {
        depth: d,
        bevelEnabled: true,
        bevelSegments: 4,
        steps: 1,
        bevelSize: r / 2,
        bevelThickness: r / 2,
      };
      return new THREE.ExtrudeGeometry(shape, extrudeSettings);
    };

    if (productType === 'laptop') {
      const baseGeo = createRoundedBox(3.4, 2.3, 0.08, 0.08);
      const base = new THREE.Mesh(baseGeo, bodyMat);
      base.rotation.x = Math.PI / 2;
      base.position.y = -0.5;
      base.castShadow = true;
      modelGroup.add(base);

      const screenLid = new THREE.Mesh(baseGeo, bodyMat);
      screenLid.position.set(0, 0.6, -1.1);
      screenLid.rotation.x = 0.25;
      screenLid.castShadow = true;
      modelGroup.add(screenLid);
    } else if (productType === 'headphone') {
      const bandGeo = new THREE.TorusGeometry(1.5, 0.08, 16, 64, Math.PI);
      const band = new THREE.Mesh(bandGeo, bodyMat);
      band.position.y = 0.5;
      modelGroup.add(band);

      const cupGeo = new THREE.CylinderGeometry(0.7, 0.75, 0.4, 32);
      const leftCup = new THREE.Mesh(cupGeo, bodyMat);
      leftCup.rotation.z = Math.PI / 2;
      leftCup.position.set(-1.5, 0.4, 0);
      leftCup.castShadow = true;
      modelGroup.add(leftCup);

      const rightCup = leftCup.clone();
      rightCup.position.set(1.5, 0.4, 0);
      rightCup.castShadow = true;
      modelGroup.add(rightCup);
    } else {
      // Smartphone Flagship Chassis
      const bodyGeo = createRoundedBox(1.9, 3.8, 0.22, 0.16);
      const body = new THREE.Mesh(bodyGeo, bodyMat);
      body.castShadow = true;
      modelGroup.add(body);

      // Gorilla Glass Display
      const screenGeo = new THREE.PlaneGeometry(1.76, 3.65);
      const screenMat = new THREE.MeshStandardMaterial({
        color: 0x030712,
        roughness: 0.05,
        metalness: 0.1,
      });
      const screen = new THREE.Mesh(screenGeo, screenMat);
      screen.position.z = 0.115;
      modelGroup.add(screen);

      // Camera Lens Array
      const bumpGeo = createRoundedBox(0.95, 0.95, 0.12, 0.12);
      const bump = new THREE.Mesh(bumpGeo, bodyMat);
      bump.position.set(-0.38, 1.25, -0.14);
      modelGroup.add(bump);

      [-0.58, -0.2].forEach((x, i) => {
        const ringGeo = new THREE.CylinderGeometry(0.16, 0.16, 0.08, 32);
        const ringMat = new THREE.MeshStandardMaterial({ color: 0x1e293b, metalness: 0.9, roughness: 0.1 });
        const ring = new THREE.Mesh(ringGeo, ringMat);
        ring.rotation.x = Math.PI / 2;
        ring.position.set(x, i === 0 ? 1.45 : 1.25, -0.22);
        modelGroup.add(ring);
      });
    }

    // Animation Loop
    let reqId: number;
    const animate = () => {
      reqId = requestAnimationFrame(animate);

      if (isAutoRotate && modelGroupRef.current) {
        modelGroupRef.current.rotation.y += 0.005;
      }

      controls.update();
      renderer.render(scene, camera);
    };
    animate();

    const handleResize = () => {
      if (!mountRef.current) return;
      const w = mountRef.current.clientWidth;
      const h = mountRef.current.clientHeight || 420;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };

    window.addEventListener('resize', handleResize);

    return () => {
      cancelAnimationFrame(reqId);
      window.removeEventListener('resize', handleResize);
      renderer.dispose();
    };
  }, [productType]);

  // Color Switcher Handler
  const handleColorChange = (hex: string) => {
    setCurrentColor(hex);
    if (bodyMaterialRef.current) {
      bodyMaterialRef.current.color.set(hex);
    }
  };

  // Wireframe Toggle
  const handleWireframeToggle = () => {
    const next = !isWireframe;
    setIsWireframe(next);
    if (bodyMaterialRef.current) {
      bodyMaterialRef.current.wireframe = next;
    }
  };

  // Lighting Mode Cycle
  const handleLightingCycle = () => {
    if (!lightsRef.current || !sceneRef.current) return;
    const modes: Array<'studio' | 'neutral' | 'warm'> = ['studio', 'neutral', 'warm'];
    const nextIdx = (modes.indexOf(lightMode) + 1) % modes.length;
    const nextMode = modes[nextIdx];
    setLightMode(nextMode);

    const { ambient, key, fill } = lightsRef.current;

    if (nextMode === 'neutral') {
      sceneRef.current.background = new THREE.Color(0x0a0e17);
      ambient.intensity = 0.7;
      key.color.setHex(0xf8fafc);
      fill.color.setHex(0xe2e8f0);
    } else if (nextMode === 'warm') {
      sceneRef.current.background = new THREE.Color(0x140e0b);
      ambient.intensity = 0.75;
      key.color.setHex(0xfde047);
      fill.color.setHex(0xf97316);
    } else {
      sceneRef.current.background = new THREE.Color(0x0b0f17);
      ambient.intensity = 0.85;
      key.color.setHex(0xffffff);
      fill.color.setHex(0x93c5fd);
    }
  };

  const handleResetCamera = () => {
    if (controlsRef.current && cameraRef.current) {
      cameraRef.current.position.set(0, 0.4, 7.5);
      controlsRef.current.reset();
    }
  };

  const finishes = [
    { label: 'Titanium Silver', hex: '#94a3b8' },
    { label: 'Space Graphite', hex: '#1e293b' },
    { label: 'Midnight Blue', hex: '#1e3a8a' },
    { label: 'Desert Dune', hex: '#c9a24b' },
    { label: 'Emerald Sage', hex: '#0f766e' },
  ];

  return (
    <div className="relative rounded-2xl overflow-hidden glass-surface shadow-tactile-lg">
      {/* Top Banner Tag */}
      <div className="absolute top-4 left-4 z-10 flex items-center gap-2 px-3 py-1 rounded-md bg-slate-900/90 border border-slate-700/60 text-xs font-medium text-slate-300">
        <span className="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse" />
        WebGL 360 Studio &bull; Drag to inspect
      </div>

      {/* 3D WebGL Canvas */}
      <div ref={mountRef} className="w-full h-[320px] sm:h-[420px] cursor-grab active:cursor-grabbing" />

      {/* Studio Controls Toolbar */}
      <div className="p-4 bg-slate-950/90 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
        {/* Color Palette */}
        <div className="flex items-center gap-2.5">
          <span className="text-xs font-semibold uppercase tracking-wider text-slate-400">Finish:</span>
          <div className="flex items-center gap-1.5">
            {finishes.map((f) => (
              <button
                key={f.hex}
                type="button"
                title={f.label}
                onClick={() => handleColorChange(f.hex)}
                className={`w-7 h-7 sm:w-6 sm:h-6 min-w-[28px] min-h-[28px] rounded-full border-2 transition-all ${
                  currentColor === f.hex
                    ? 'border-white scale-110 shadow-sm'
                    : 'border-slate-700 hover:scale-105'
                }`}
                style={{ backgroundColor: f.hex }}
              />
            ))}
          </div>
        </div>

        {/* Action Controls */}
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={() => setIsAutoRotate(!isAutoRotate)}
            className={`px-3 py-1.5 min-h-[38px] rounded-lg text-xs font-medium flex items-center gap-1.5 border transition-all ${
              isAutoRotate
                ? 'bg-blue-600 border-blue-500 text-white shadow-tactile'
                : 'bg-slate-900 border-slate-700 text-slate-300 hover:bg-slate-800'
            }`}
          >
            <RotateCw className="w-3.5 h-3.5" />
            Rotate
          </button>

          <button
            type="button"
            onClick={handleLightingCycle}
            className="px-3 py-1.5 min-h-[38px] rounded-lg text-xs font-medium flex items-center gap-1.5 bg-slate-900 border border-slate-700 text-slate-300 hover:bg-slate-800 transition-all capitalize"
          >
            <Sun className="w-3.5 h-3.5 text-amber-400" />
            {lightMode}
          </button>

          <button
            type="button"
            onClick={handleWireframeToggle}
            className={`px-3 py-1.5 min-h-[38px] rounded-lg text-xs font-medium flex items-center gap-1.5 border transition-all ${
              isWireframe
                ? 'bg-slate-800 border-slate-600 text-blue-300'
                : 'bg-slate-900 border-slate-700 text-slate-300 hover:bg-slate-800'
            }`}
          >
            <Box className="w-3.5 h-3.5" />
            Mesh
          </button>

          <button
            type="button"
            onClick={handleResetCamera}
            title="Reset Angle"
            className="p-2 sm:p-1.5 min-h-[38px] min-w-[38px] flex items-center justify-center rounded-lg bg-slate-900 border border-slate-700 text-slate-400 hover:text-white transition-all"
          >
            <RefreshCcw className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>
  );
}