activeCollab Notify 
================

ActiveCollab integration with the Notification Center.

- Get RSS feed
- If new item
  - Pass data to notification center
- if not new item
  - Expire data from cache?

- Config
  - Set up frequency of checking for data (in minutes)
    - write to plist

- Requirements
  - terminal-notification
  - lunchy ?

- Commands
  - Service
    - argument: Start
      - starts monitoring
    - argument: Stop
      - stops monitoring
  - Get updates
    - output -> notification center
