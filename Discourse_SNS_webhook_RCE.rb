
# frozen_string_literal: true

require 'faraday'
require 'aws-sdk-sns'
require 'open-uri'
require 'base64'

module Jobs
  class ConfirmSnsSubscription < ::Jobs::Base
    sidekiq_options retry: false

    def execute(args)
      return unless raw = args[:raw].presence
      return unless json = args[:json].presence
      return unless subscribe_url = json["SubscribeURL"].presence

      return unless Aws::SNS::MessageVerifier.new.authentic?(raw)

      # validate user input
      return unless valid_input?(subscribe_url)

      # authenticate and authorize user
      return unless authenticate_and_authorize_user?(current_user, subscribe_url)

      # encrypt data
      cipher = OpenSSL::Cipher::AES.new(256, :CBC)
      cipher.encrypt
      cipher.key = SecureRandom.random_bytes(32)
      iv = cipher.random_iv
      encrypted = cipher.update(subscribe_url) + cipher.final
      encrypted_url = Base64.encode64(iv + encrypted)

      # generate a secure token
      token = SecureRandom.hex

      # add the token to the URL
      encrypted_url = encrypted_url + "?token=#{token}"

      # confirm subscription by visiting the URL
      response = Faraday.get(encrypted_url) do |req|
        req.options.ssl.verify = true
        req.headers['Authorization'] = "Basic #{Base64.strict_encode64("#{ENV['CONFIRM_SNS_USERNAME']}:#{ENV['CONFIRM_SNS_PASSWORD']}")}"
      end
    end

    private

    def valid_input?(url)
      uri = URI.parse(url)
      scheme_valid = ['https'].include?(uri.scheme)
      host_valid = uri.host.present?
      path_valid = uri.path.length < 100 && uri.path.match?(/\A[a-zA-Z0-9\.\-_\/]*\z/)
      query_valid = uri.query.nil? || (uri.query.length < 100 && uri.query.match?(/\A[a-zA-Z0-9\.\-\_%&\=]*\z/))
      scheme_valid && host_valid && path_valid && query_valid
    rescue URI::InvalidURIError
      false
    end

    def authenticate_and_authorize_user?(user, target)
      # authenticate user
      return false unless user.present?

      # authorize user to perform the operation
      # implement your authorization logic here, e.g., with Devise and Pundit
      # return false unless Pundit.policy(user, target).confirm_sns_subscription?

      true
    end
  end
end
