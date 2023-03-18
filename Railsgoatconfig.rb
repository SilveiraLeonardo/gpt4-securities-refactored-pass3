
# config/secrets.yml
development:
  secret_key_base: <%= SecureRandom.hex(64) %>
test:
  secret_key_base: <%= SecureRandom.hex(64) %>
production:
  secret_key_base: <%= ENV["SECRET_KEY_BASE"] %>
