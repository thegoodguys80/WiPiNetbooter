#!/bin/bash
# Install pre-commit hooks for WiPiNetbooter development
# Usage: ./install-hooks.sh

set -e

echo "🔧 Installing pre-commit hooks..."

# Check if pre-commit is installed
if ! command -v pre-commit &> /dev/null; then
    echo "📦 Installing pre-commit package..."
    pip3 install pre-commit
fi

# Install git hooks
echo "🪝 Installing git hooks..."
pre-commit install

# Run hooks on all files to test
echo "🧪 Testing hooks on all files..."
pre-commit run --all-files || true

echo ""
echo "✅ Pre-commit hooks installed successfully!"
echo ""
echo "Usage:"
echo "  • Hooks run automatically on 'git commit'"
echo "  • Run manually: pre-commit run --all-files"
echo "  • Skip hooks: git commit --no-verify (use sparingly)"
echo ""
