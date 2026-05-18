# 🏦 E-Banking Secure Platform

Ce projet est une application web bancaire sécurisée développée en **PHP 8**, conçue pour démontrer les principes de sécurité web (OWASP) et le déploiement Cloud (PaaS/IaaS) sur **Azure OpenShift**.

## 🚀 Fonctionnalités
- **Authentification Sécurisée** : Hachage Argon2id.
- **Gestion de Compte** : Dashboard avec solde et historique.
- **Virements Sécurisés** : Protection contre les failles CSRF et Race Conditions (Transactions SQL).
- **Architecture Conteneurisée** : Prêt pour le déploiement Docker et OpenShift.

## 🛠️ Installation et Lancement (Local)

Pour faire fonctionner ce projet sur votre machine, vous devez avoir **Docker Desktop** installé.

1. **Cloner le projet** :
   ```bash
   git clone https://github.com/Ibrxhxm-Bxddxne/Ebanking_App
   cd Ebanking_App

2. **Lancer l'application** :
   ```bash
   docker-compose up -d
